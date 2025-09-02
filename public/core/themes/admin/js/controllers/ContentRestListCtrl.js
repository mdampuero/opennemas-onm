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
      '$controller', '$scope', '$uibModal', 'oqlEncoder', '$location', 'http', '$http', 'messenger', 'routing', '$window',
      function($controller, $scope, $uibModal, oqlEncoder, $location, http, $http, messenger, routing, $window) {
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
          delete data.urldatetime;
          delete data.slug;
          data.content_status = 0;
          if (data.title) {
            data.title = 'Copy of ' + data.title;
          }

          if (data.pk_menu && data.name) {
            delete data.pk_menu;
            data.name = 'Copy of ' + data.name;
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
              var route = {
                name: 'api_v1_backend_article_get_item',
                params: { id: content.pk_content }
              };

              http.get(route).then(function(response) {
                var notificationItem = angular.extend({}, response.data.item);
                var contentNotifications = notificationItem.webpush_notifications || [];

                contentNotifications.push(
                  {
                    status: 0,
                    body: null,
                    title: notificationItem.title,
                    send_date: $window.moment.utc($window.moment()).format('YYYY-MM-DD HH:mm:ss'),
                    image: null,
                    transaction_id: null,
                    impressions: 0,
                    clicks: 0,
                    closed: 0
                  }
                );
                $scope.patch(content, 'webpush_notifications', contentNotifications)
                  .then(function() {
                    $scope.list();
                  });
              });
            }
          });
        };

        /**
         * Exports selected items.
         *
         * @param {String} route The route name.
         * @param mixed ids The ids of the items to export.
         */
        $scope.exportSelectedItems = function(route) {
          const ids         = $scope.selected.items;
          const contentType = $scope.criteria.content_type_name;

          if (ids.length === 0) {
            messenger.post(window.strings.forms.no_items_selected, 'error');
            return;
          }

          var url = routing.generate(route, { ids: ids, contentType: contentType });

          $http.get(url).then(function(response) {
            messenger.post(response.data.messages);

            window.location.href = url;
          });
        };

        /**
         * Exports list based on current criteria.
         *
         * @param {String} route The route name.
         */
        $scope.export = function(route) {
          const contentType = $scope.criteria.content_type_name;

          // Build the URL for export
          const url         = routing.generate(route, { contentType: contentType });

          $http.get(url).then(function(response) {
            messenger.post(response.data.messages);

            window.location.href = url;
          });
        };

        /**
         * Imports items from a JSON file.
         * @memberOf ContentRestListCtrl
         * @description
         *  Opens a modal to select a JSON file and imports its content.
         *  The JSON file must be in the correct format.
         * @see DataTransferController::exportAction
         * @see DataTransferController::importAction
         *
         * @return {void}
         */
        $scope.import = function() {
          $uibModal.open({
            templateUrl: 'modal-datatransfer',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {};
              },
              success: function($http) {
                return function(modal, template) {
                  if (!template.file) {
                    return messenger.post('No file selected', 'error');
                  }

                  if (template.file.type !== 'application/json') {
                    return messenger.post('No es un fichero JSON válido', 'error');
                  }

                  const reader = new FileReader();

                  reader.readAsText(template.file);

                  reader.onload = function(event) {
                    const json = JSON.parse(event.target.result);

                    const url = routing.generate('api_v1_backend_datatransfer_import');

                    $http.post(url, json, {
                      headers: { 'Content-Type': 'application/json' },
                      transformRequest: angular.toJson
                    }).then(function(response) {
                      messenger.post(response.data);
                      $scope.list($scope.route);
                    });
                  };
                };
              }
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
