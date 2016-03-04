(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name UserListCtrl
     *
     * @requires $controller
     * @requires $uibModal
     * @requires $scope
     * @requiers $timeout
     * @requires itemService
     * @requires routing
     * @requires messenger
     * @requires data
     *
     * @description
     *   Handles all actions in users listing.
     */
    .controller('UserListCtrl', [
      '$controller', '$uibModal', '$scope', '$timeout', 'itemService', 'routing', 'messenger', 'data',
      function ($controller, $uibModal, $scope, $timeout, itemService, routing, messenger, data) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope:   $scope,
          $timeout: $timeout,
          data:     data
        }));

        /**
         * @memberOf UserListCtrl
         *
         * @description
         *   The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          name: [ { value: '', operator: 'like' } ],
          fk_user_group: [ { value: '-1', operator: 'regexp' } ]
        };

        /**
         * @memberOf UserListCtrl
         *
         * @description
         *   The list order.
         *
         * @type {Object}
         */
        $scope.orderBy = [ { name: 'name', value: 'asc' } ];

        /**
         * @function deleteSelected
         * @memberOf UserListCtrl
         *
         * @description
         *   Confirm delete action.
         */
        $scope.delete = function(user) {
          var modal = $uibModal.open({
            templateUrl: 'modal-confirm',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  name: 'delete-user'
                };
              },
              success: function() {
                return function(modalInstance) {
                  return itemService.delete('manager_ws_user_delete', user.id)
                    .success(function(response) {
                      modalInstance.close({ message: response, type: 'success'});
                    }).error(function(response) {
                      modalInstance.close({ message: response, type: 'error'});
                    });
                };
              }
            }
          });

          modal.result.then(function(response) {
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
                var selected = [];

                for (var i = 0; i < $scope.items.length; i++) {
                  if ($scope.selected.items.indexOf(
                        $scope.items[i].id) !== -1) {
                    selected.push($scope.items[i]);
                  }
                }

                return {
                  name: 'delete-users',
                  selected: selected
                };
              },
              success: function() {
                return function(modalInstance) {
                  itemService.deleteSelected('manager_ws_users_delete',
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

          // Search by name, domains and contact mail
          if (cleaned.name) {
            cleaned.username = cleaned.name;

            // OR operator
            cleaned.union = 'OR';
          }

          var data = {
            criteria: cleaned,
            orderBy:  $scope.orderBy,
            epp:      $scope.pagination.epp,
            page:     $scope.pagination.page
          };

          itemService.encodeFilters($scope.criteria, $scope.orderBy,
              $scope.pagination.epp, $scope.pagination.page);

          itemService.list('manager_ws_users_list', data).then(
            function (response) {
              $scope.items   = response.data.results;
              $scope.pagination.total   = response.data.total;
              $scope.loading = 0;

              // Scroll top
              $('.page-content').animate({ scrollTop: '0px' }, 1000);
            }
          );
        };

            /**
             * Enables/disables an user.
             *
             * @param boolean enabled Instance activated value.
             */
            $scope.setEnabled = function(user, enabled) {
                user.loading = 1;

                itemService.patch('manager_ws_user_patch',
                    user.id, { activated: enabled }).then(function (response) {
                        user.loading = 0;

                        messenger.post({
                            message: response.data,
                            type: response.status == 200 ? 'success' : 'error'
                        });

                        if (response.status == 200) {
                            user.activated = enabled;
                        }
                    });
            };

            /**
             * Enables/disables the selected users.
             *
             * @param integer enabled The activated value.
             */
            $scope.setEnabledSelected = function(enabled) {
                for (var i = 0; i < $scope.items.length; i++) {
                    var id = $scope.items[i].id;
                    if ($scope.selected.items.indexOf(id) != -1) {
                        $scope.items[i].loading = 1;
                    }
                }

                var data = {
                    selected: $scope.selected.items,
                    activated: enabled
                };

                itemService.patchSelected('manager_ws_users_patch', data).then(function (response) {
                    if (response.status === 200 || response.status === 207) {
                        // Update users changed successfully
                        for (var i = 0; i < $scope.items.length; i++) {
                            var id = $scope.items[i].id;

                            if (response.data.success.ids.indexOf(id) !== -1) {
                                $scope.items[i].activated = enabled;
                                delete $scope.items[i].loading;
                            }
                        }

                        // Show success message
                        if (response.data.success.ids.length > 0) {
                          messenger.post({
                              message: response.data.success.message,
                              type: 'success'
                          });
                        }

                        // Show errors
                        for (var i = 0; i < response.data.errors.length; i++) {
                            var params = {
                                message: response.data.error[i].message,
                                type:    'error'
                            };

                            messenger.post(params);
                        }
                    }
                });
            };

            /**
             * Refresh the list of elements when some parameter changes.
             *
             * @param array newValues The new values
             * @param array oldValues The old values
             */
            $scope.$watch('[criteria.fk_user_group]', function(newValues, oldValues) {
                if (newValues !== oldValues) {
                    list();
                }
            }, true);

            // Initialize filters from URL
            var filters = itemService.decodeFilters();
            for(var name in filters) {
                $scope[name] = filters[name];
            }
        }
    ]);
})();
