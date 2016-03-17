(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  UserGroupListCtrl
     *
     * @requires $controller
     * @requires $uibModal
     * @requires $location
     * @requires $scope
     * @requires $timeout
     * @requires http
     * @requires routing
     * @requires messenger
     * @requires oqlBuilder
     * @requires data
     *
     * @description
     *   Handles all actions in user groups list.
     */
    .controller('UserGroupListCtrl', [
      '$controller', '$uibModal', '$location', '$scope', '$timeout', 'http', 'routing', 'messenger', 'oqlBuilder', 'data',
      function ($controller, $uibModal, $location, $scope, $timeout, http, routing, messenger, oqlBuilder, data) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope:   $scope,
          $timeout: $timeout,
          data:     data
        }));

        /**
         * @memberOf UserGroupListCtrl
         *
         * @description
         *   The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = { epp: 25, page: 1 };

        /**
         * @function delete
         * @memberOf UserGroupListCtrl
         *
         * @description
         *   Confirm delete action.
         *
         * @param {Object} group The group to delete.
         */
        $scope.delete = function(id) {
          var modal = $uibModal.open({
            templateUrl: 'modal-confirm',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  name: 'delete-user-group'
                };
              },
              success: function() {
                return function(modalInstance) {
                  return itemService.delete('manager_ws_user_group_delete', id)
                    .success(function(response) {
                      modalInstance.close({ message: response, type: 'success'});
                    }).error(function(response) {
                      modalInstance.close({ message: response, type: 'error'});
                    });
                };
              }
            }
          });

          modal.result.then(function (response) {
            messenger.post(response);
            $scope.list();
          });
        };

        /**
         * @function deleteSelected
         * @memberOf InstanceListCtrl
         *
         * @description
         *   Confirm delete action.
         */
        $scope.deleteSelected = function() {
          var modal = $uibModal.open({
            templateUrl: 'modal-confirm',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  name: 'delete-user-groups'
                };
              },
              success: function() {
                return function(modalInstance) {
                  return itemService.deleteSelected('manager_ws_user_groups_delete',
                      $scope.selected.items).success(function(response) {
                        modalInstance.close(response);
                    }).error(function(response) {
                        modalInstance.close(response);
                    });
                };
              }
            }
          });

          modal.result.then(function (response) {
            if (response.messages) {
              messenger.post(response.messages);

              $scope.selected = { all: false, items: [] };
            } else {
              messenger.post(response);
            }

            $scope.list();
          });
        };

        /**
         * @function list
         * @memberOf InstanceListCtrl
         *
         * @description
         *   Reloads the list.
         */
        $scope.list = function () {
          $scope.loading = 1;

          oqlBuilder.configure({ placeholder: { name: '[key] ~ "[value]"' } });

          var oql    = oqlBuilder.getOql($scope.criteria);
          var route  = {
            name: 'manager_ws_user_groups_list',
            params: { oql: oql }
          };

          $location.search('oql', oql);

          http.get(route).then(function (response) {
            $scope.items           = response.data.results;
            $scope.pagination.total = response.data.total;
            $scope.loading          = 0;

            // Scroll top
            $('.page-content').animate({ scrollTop: '0px' }, 1000);
          });
        };
      }
    ]);
})();
