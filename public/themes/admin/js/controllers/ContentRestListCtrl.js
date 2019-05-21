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
         * @function select
         * @memberOf AlbumListCtrl
         *
         * @description
         *   Adds and removes the item from the selected array.
         */
        $scope.select = function(item) {
          if ($scope.selected.items.indexOf($scope.getId(item)) < 0) {
            $scope.selected.items.push($scope.getId(item));
          } else {
            $scope.selected.items = $scope.selected.items.filter(function(el) {
              return el !== $scope.getId(item);
            });
          }
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

        /**
         * Updates the criteria.page, used in listings with mode == grid.
         */
        $scope.scroll = function() {
          if ($scope.total === $scope.items.length) {
            return;
          }

          $scope.criteria.page++;
          $scope.$apply();
        };

        /**
         * Updates the array of contents.
         *
         * @param {String}  route The route name.
         * @param {Boolean} reset Whether to reset the list.
         */
        $scope.list = function(route, reset) {
          if (!reset && $scope.app.mode === 'grid') {
            $scope.flags.loadingMore = 1;
          } else {
            if ($scope.app.mode === 'grid') {
              $scope.flags.loadingMore = 1;
            } else {
              $scope.flags.http.loading  = 1;
            }

            if ($scope.data) {
              $scope.items      = [];
              $scope.data.items = [];
            }
          }

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: $scope.routes.list,
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            if (reset || $scope.app.mode === 'grid') {
              $scope.data = $scope.data ? $scope.data : { extra: [], items: [] };

              // Merge items
              response.data.items = [].concat($scope.data.items, response.data.items);

              // Merge extra info with the scope
              for (var key in response.data.extra) {
                if (angular.isArray(response.data.extra[key]) &&
                    angular.isArray($scope.data.extra[key])) {
                  response.data.extra[key] = [].concat($scope.data.extra[key],
                    response.data.extra[key]);
                }

                if (angular.isObject(response.data.extra[key]) &&
                    angular.isObject($scope.data.extra[key])) {
                  response.data.extra[key] = angular.merge($scope.data.extra[key],
                    response.data.extra[key]);
                }
              }
            }

            $scope.data = response.data;

            $scope.parseList(response.data);

            $scope.disableFlags('http');
            $scope.disableFlags('loadingMore');

            // Scroll top
            if ($scope.app.mode !== 'grid') {
              $('body').animate({ scrollTop: '0px' }, 1000);
            }
          }, function(response) {
            messenger.post(response.data);

            $scope.disableFlags('http');
            $scope.disableFlags('loadingMore');
            $scope.data = {};
          });
        };

        // Reloads the list when criteria changes
        $scope.$watch('criteria', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          var changes = [];

          // Get which values change ignoring page
          for (var key in $scope.criteria) {
            if (key !== 'page' && !angular.equals(nv[key], ov[key])) {
              changes.push(key);
            }
          }

          // Reset the list if search changes
          var reset = changes.length > 0;

          // Change page when scrolling in grid mode
          if ($scope.tm) {
            $timeout.cancel($scope.tm);
          }

          if (ov.page === nv.page) {
            $scope.criteria.page = 1;
          }

          $scope.tm = $timeout(function() {
            $scope.list($scope.routes.list, reset);
          }, 500);
        }, true);

        // Change page when scrolling in grid mode
        $(window).scroll(function() {
          if ($scope.app.mode === 'list' ||
            $scope.items.length === $scope.data.total) {
            return;
          }

          if (!$scope.flags.http.loadingMore && $(document).height() <=
              $(window).height() + $(window).scrollTop()) {
            $scope.scroll();
          }
        });
      }
    ]);
})();
