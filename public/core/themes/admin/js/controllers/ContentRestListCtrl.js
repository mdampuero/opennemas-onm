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
      '$controller', '$scope', '$uibModal', 'oqlEncoder', '$location', 'http', 'messenger', '$timeout',
      function($controller, $scope, $uibModal, oqlEncoder, $location, http, messenger, $timeout) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @inheritdoc
         */
        $scope.getItemId = function(item) {
          return item.pk_content;
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
         * @function sendToTrash
         * @memberOf UserCtrl
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
      }
    ]);
})();
