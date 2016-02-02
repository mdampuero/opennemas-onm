(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  InstanceListCtrl
     *
     * @requires $modal
     * @requires $scope
     * @requires itemService
     * @requires routing
     * @requires messenger
     * @requires webStorage
     * @requires data
     *
     * @description
     *   Handles all actions in instances list.
     */
    .controller('InstanceListCtrl', [
      '$controller', '$modal', '$scope', '$timeout', 'itemService', 'routing', 'messenger', 'webStorage', 'data',
      function($controller, $modal, $scope, $timeout, itemService, routing, messenger, webStorage, data) {

        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope:   $scope,
          $timeout: $timeout,
          data:     data
        }));

        /**
         * @memberOf InstanceListCtrl
         *
         * @description
         *   The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = { name_like: [] };

        /**
         * @memberOf InstanceListCtrl
         *
         * @description
         *   The visible table columns.
         *
         * @type {Object}
         */
        $scope.columns = {
          collapsed: 1,
          selected: [
            'name', 'domains', 'last_login', 'created',
            'articles', 'alexa', 'activated'
          ]
        };

        /**
         * @memberOf InstanceListCtrl
         *
         * @description
         *   The list order.
         *
         * @type {Object}
         */
        $scope.orderBy = [{ name: 'last_login', value: 'desc' }];

        /**
         * @function delete
         * @memberOf InstanceListCtrl
         *
         * @description
         *   Confirm delete action.
         *
         * @param {Object} instance The instance to delete.
         */
        $scope.delete = function(instance) {
          var modal = $modal.open({
            templateUrl: 'modal-confirm',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  name: 'delete-instance',
                  item: instance
                };
              },
              success: function() {
                return function(modalInstance) {
                  itemService.delete('manager_ws_instance_delete', instance.id)
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
          var modal = $modal.open({
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
                  name: 'delete-instances',
                  selected: selected
                };
              },
              success: function() {
                return function(modalInstance) {
                  itemService.deleteSelected('manager_ws_instances_delete',
                      $scope.selected.items).success(function(response) {
                        modalInstance.close(response);
                      }).error(function(response) {
                        modalInstance.close(response);
                      });
                };
              }
            }
          });

          modal.result.then(function(response) {
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

          // Search by name, domains and contact mail
          if ($scope.criteria.name_like) {
            $scope.criteria.domains_like =
              $scope.criteria.contact_mail_like =
              $scope.criteria.name_like;
          }

          var cleaned = itemService.cleanFilters($scope.criteria);

          var data = {
            criteria: cleaned,
            orderBy: $scope.orderBy,
            epp: $scope.pagination.epp, // elements per page
            page: $scope.pagination.page
          };

          itemService.encodeFilters($scope.criteria, $scope.orderBy,
            $scope.pagination.epp, $scope.pagination.page);

          itemService.list('manager_ws_instances_list', data).then(
            function(response) {
              $scope.items = response.data.results;
              $scope.pagination.total = response.data.total;

              $scope.loading = 0;

              // Scroll top
              $('.page-content').animate({ scrollTop: '0px' }, 1000);
            }
          );
        };

        /**
         * @function isEnabled
         * @memberOf InstanceListCtrl
         *
         * @description
         *   Enables/disables an instance.
         *
         * @param boolean enabled Instance activated value.
         */
        $scope.setEnabled = function(instance, enabled) {
          instance.loading = 1;

          itemService.patch('manager_ws_instance_patch', instance.id,
            { activated: enabled }).success(function(response) {
              instance.loading = 0;
              instance.activated = enabled;

              messenger.post({ message: response, type: 'success' });
            }).error(function(response) {
              messenger.post({ message: response, type: 'error' });
            });
        };

        /**
         * @function isEnabled
         * @memberOf InstanceListCtrl
         *
         * @description
         *   Enables/disables the selected instances.
         *
         * @param integer enabled The activated value.
         */
        $scope.setEnabledSelected = function(enabled) {
          for (var i = 0; i < $scope.items.length; i++) {
            var id = $scope.items[i].id;
            if ($scope.selected.items.indexOf(id) !== -1) {
              $scope.items[i].loading = 1;
            }
          }

          var data = { selected: $scope.selected.items, activated: enabled };

          itemService.patchSelected('manager_ws_instances_patch', data)
            .success(function(response) {
              // Update instances changed successfully
              for (var i = 0; i < $scope.items.length; i++) {
                var id = $scope.items[i].id;

                if (response.success.indexOf(id) !== -1) {
                  $scope.items[i].activated = enabled;
                  delete $scope.items[i].loading;
                }
              }

              if (response.messages) {
                messenger.post(response.messages);

                $scope.selected = { all: false, items: [] };
              } else {
                messenger.post(response);
              }

              if (response.success.length > 0) {
                $scope.list();
              }
            }).error(function(response) {
              // Update instances changed successfully
              for (var i = 0; i < $scope.items.length; i++) {
                delete $scope.items[i].loading;
              }

              if (response.messages) {
                messenger.post(response.messages);

                $scope.selected = { all: false, items: [] };
              } else {
                messenger.post(response);
              }
            });
        };

        // Updates the columns stored in localStorage.
        $scope.$watch('columns', function(newValues, oldValues) {
          if (newValues !== oldValues) {
            webStorage.local.add('instances-columns', $scope.columns);
          }
        }, true);

        // Initialize filters from URL
        var filters = itemService.decodeFilters();
        for (var name in filters) {
          $scope[name] = filters[name];
        }

        // Get enabled columns from localStorage
        if (webStorage.local.get('instances-columns')) {
          $scope.columns = webStorage.local.get('instances-columns');
        }

        if (webStorage.local.get('token')) {
          $scope.token = webStorage.local.get('token');
        }
      }
    ]);
})();

