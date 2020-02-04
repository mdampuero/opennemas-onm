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
         * Updates the array of contents.
         *
         * @param {Boolean} reset Whether to reset the list.
         */
        $scope.list = function(reset) {
          if (!$scope.isModeSupported() || reset || $scope.app.mode === 'list') {
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
            if ($scope.isModeSupported() && !reset && $scope.app.mode === 'grid') {
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
          }, function(response) {
            messenger.post(response.data);
            $scope.disableFlags('http');
            $scope.data = {};
          });
        };

        // Change page when scrolling in grid mode
        $(window).scroll(function() {
          if (!$scope.isModeSupported() || $scope.app.mode === 'list' ||
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
