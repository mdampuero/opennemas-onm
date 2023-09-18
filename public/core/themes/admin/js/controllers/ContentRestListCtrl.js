(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ContentRestListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $uibModal
     *
     * @description
     *   Handles all actions in user groups list.
     */
    .controller('ContentRestListCtrl', [
      '$controller', '$scope', '$uibModal', 'oqlEncoder', '$location', 'http', 'messenger', '$window',
      function($controller, $scope, $uibModal, oqlEncoder, $location, http, messenger, $window) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @inheritdoc
         */
        $scope.getItemId = function(item) {
          return item.pk_content;
        };

        /**
         * @function getLocalizedTags
         * @memberof ContentRestListCtrl
         *
         * @description
         *  Returns localized tags for each item in list
         *
         * @param {array} origin The list of all localized tags
         * @param {array} array The list of item id tags
         *
         * @return {array} return tags localized
         */
        $scope.getLocalizedTags = function(origin, array, locale, multilanguage) {
          if (multilanguage) {
            return origin[locale].filter(function(o) {
              return array.includes(o.id);
            });
          }

          return origin.filter(function(o) {
            return array.includes(o.id);
          });
        };

        /**
         * @function createCopy
         * @memberof ContentRestListCtrl
         *
         * @description
         *  Returns localized tags for each item in list
         *
         * @param {array} data Data to create a copy
         *
         */
        $scope.createCopy = function(item) {
          var modal = $uibModal.open({
            templateUrl: 'modal-duplicate',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return { };
              },
              success: function() {
                return null;
              }
            }
          });

          modal.result.then(function(response) {
            if (response) {
              var route = $scope.routes.saveItem;
              var data  = $scope.parseDataForCopy(item);

              http.post(route, data)
                .then(function() {
                  $scope.list();
                }, $scope.errorCb);
            }
          });
        };

        /**
         * @function parseDataForCopy
         * @memberOf ArticleListCtrl
         *
         * @description
         *   Parse data before copy
         */
        $scope.parseDataForCopy = function(data) {
          delete data.pk_content;
          delete data.urn_source;
          delete data.starttime;
          delete data.endtime;
          delete data.slug;
          data.content_status = 0;
          if (data.title) {
            data.title = 'Copy of ' + data.title;
          }

          return data;
        };

        /**
         * @function hasFeaturedMedia
         * @memberof ContentRestListCtrl
         *
         * @description
         *  Returns true if the content has featured media.
         *
         * @param {Object} item The item to get featured media for.
         * @param {String} type The featured media type.
         *
         * @return {Boolean} True if the item has featured media, false otherwise.
         */
        $scope.hasFeaturedMedia = function(item, type) {
          return $scope.getFeaturedMedia(item, type).path !== null;
        };

        /**
         * @inheritdoc
         */
        $scope.hasMultilanguage = function() {
          return $scope.config && $scope.config.locale &&
            $scope.config.locale.multilanguage;
        };

        /**
         * @function sendWPNotification
         * @memberOf ContentRestInnerCtrl
         *
         * @description
         *   Send webpush notification to all subscribers
         */
        $scope.sendWPNotification = function(content) {
          var modal = $uibModal.open({
            templateUrl: 'modal-webpush',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return { status: 1 };
              },
              success: function() {
                return null;
              }
            }
          });

          modal.result.then(function(response) {
            if (response && content) {
              var contentNotifications = content.webpush_notifications || [];
              var image = content.related_contents[0] ? content.related_contents[0].target_id : null;

              contentNotifications.push(
                {
                  status: 1,
                  body: content.description,
                  title: content.title,
                  send_date: $window.moment.utc($window.moment()).format('YYYY-MM-DD HH:mm:ss'),
                  image: image,
                }
              );
              $scope.patch(content, 'webpush_notifications', contentNotifications)
                .then(function() {
                  http.post('send_notification', [ content.pk_content ]);
                  $scope.list();
                });
            }
          });
        };

        /**
         * @function sendToTrash
         * @memberOf ContentRestListCtrl
         *
         * @description
         *   Shows a modal to confirm user update.
         */
        $scope.sendToTrash = function(item) {
          var modal = $uibModal.open({
            templateUrl: 'modal-delete',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {
                  name:     $scope.id ? 'update' : 'create',
                  selected: $scope.selected.items.length,
                  value:    1,
                };
              },
              success: function() {
                return null;
              }
            }
          });

          modal.result.then(function(response) {
            if (response) {
              if (item) {
                $scope.patch(item, 'in_litter', 1)
                  .then(function() {
                    $scope.list();
                  });
                return;
              }

              $scope.patchSelected('in_litter', 1)
                .then(function() {
                  $scope.list();
                });
            }
          });
        };

        $scope.$watch('postponed', function(nv) {
          if (nv && nv === true) {
            var date = $window.moment().format('YYYY-MM-DD HH:mm:ss');

            $scope.criteria.content_status = 1;
            $scope.criteria.starttime = date;
          } else if (nv === false) {
            $scope.criteria.starttime = null;
          }
        });

        /**
         * Reloads the image list on media picker close event.
         */
        $scope.$on('MediaPicker.close', function() {
          if ($scope.criteria.content_type_name === 'photo') {
            $scope.list($scope.route, true);
          }
        });

        /**
         * Updates the array of contents.
         */
        $scope.list = function() {
          if (!$scope.isModeSupported() || $scope.app.mode === 'list') {
            $scope.flags.http.loading = 1;
          } else {
            $scope.flags.http.loadingMore = 1;
          }

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: $scope.routes.getList,
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            $scope.data = response.data;
            $scope.parseList(response.data);
            $scope.disableFlags('http');
          }, function(response) {
            messenger.post(response.data);
            $scope.disableFlags('http');
            $scope.data  = {};
            $scope.items = [];
          });
        };

        /**
         * @function localizeText
         * @memberOf ContentRestListCtrl
         *
         * @param {any} String or Object to localize.
         *
         * @return {String} Localized text.
         *
         * @description
         *   Localize and return text
         */
        $scope.localizeText = function(text) {
          if (typeof text === 'object') {
            return text[$scope.config.locale.selected];
          }

          return text;
        };
      }
    ]);
})();
