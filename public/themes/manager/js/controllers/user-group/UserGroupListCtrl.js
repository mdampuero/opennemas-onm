(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  UserGroupListCtrl
     *
     * @requires $controller
     * @requires $uibModal
     * @requires $scope
     * @requires $timeout
     * @requires itemService
     * @requires routing
     * @requires messenger
     * @requires data
     *
     * @description
     *   Handles all actions in user groups list.
     */
    .controller('UserGroupListCtrl', [
      '$controller', '$uibModal', '$scope', '$timeout', 'itemService', 'routing', 'messenger', 'data',
      function ($controller, $uibModal, $scope, $timeout, itemService, routing, messenger, data) {
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
        $scope.criteria = {
          name_like: [ { value: '', operator: 'like' } ]
        };

        /**
         * @memberOf UserGroupListCtrl
         *
         * @description
         *   The list order.
         *
         * @type {Object}
         */
        $scope.orderBy = [ { name: 'name', value: 'asc' } ];

        /**
         * @function delete
         * @memberOf UserGroupListCtrl
         *
         * @description
         *   Confirm delete action.
         *
         * @param {Object} group The group to delete.
         */
        $scope.delete = function(group) {
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
                  return itemService.delete('manager_ws_user_group_delete', group.id)
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

          var cleaned = itemService.cleanFilters($scope.criteria);

          var data = {
            criteria: cleaned,
            orderBy:  $scope.orderBy,
            epp:      $scope.pagination.epp,
            page:     $scope.pagination.page
          };

          itemService.encodeFilters($scope.criteria, $scope.orderBy,
              $scope.epp, $scope.pagination.page);

          itemService.list('manager_ws_user_groups_list', data).then(
            function (response) {
              $scope.items           = response.data.results;
              $scope.pagination.total = response.data.total;
              $scope.loading          = 0;

              // Scroll top
              $('.page-content').animate({ scrollTop: '0px' }, 1000);
            }
          );
        };
      }
    ]);
})();
