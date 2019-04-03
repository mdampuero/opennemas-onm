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
      '$controller', '$scope', '$uibModal', 'oqlEncoder', '$location', 'http', 'messenger',
      function($controller, $scope, $uibModal, oqlEncoder, $location, http, messenger) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @function getId
         * @memberOf ContentistCtrl
         *
         * @description
         *   Returns the item id.
         *
         * @param {Object} item The item.
         *
         * @return {Integer} The item id.
         */
        $scope.getId = function(item) {
          return item.pk_content;
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
            controller: 'modalCtrl',
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

        $scope.scroll = function() {
          if ($scope.total === $scope.items.length) {
            return;
          }

          $scope.criteria.page++;
        };

        /**
         * Updates the array of contents.
         *
         * @param {String}  route The route name.
         * @param {Boolean} reset Whether to reset the list.
         */
        $scope.list = function(route) {
          if ($scope.mode === 'grid') {
            $scope.flags.http.loadingMore = 1;
          } else {
            $scope.flags.http.loading  = 1;
          }

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: $scope.routes.list,
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            var oldItems = $scope.data.items;

            $scope.data = response.data;

            if ($scope.mode === 'grid') {
              $scope.data.items = oldItems.concat(response.data.items);
            } else {
              $scope.data.items = response.data.items;
            }

            $scope.parseList(response.data);

            $scope.disableFlags('http');
            $scope.disableFlags('loadingMore');

            // Scroll top
            $('body').animate({ scrollTop: '0px' }, 1000);
          }, function(response) {
            messenger.post(response.data);

            $scope.disableFlags('http');
            $scope.disableFlags('loadingMore');
            $scope.data.items = [];
          });
        };
      }
    ]);
})();
