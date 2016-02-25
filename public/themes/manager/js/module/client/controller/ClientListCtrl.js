(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  ClientListCtrl
     *
     * @requires $controller
     * @requires $modal
     * @requires $scope
     * @requires itemService
     * @requires routing
     * @requires messenger
     * @requires webStorage
     * @requires data
     *
     * @description
     *   Handles all actions in clients listing.
     */
    .controller('ClientListCtrl', [
      '$controller', '$modal', '$scope', '$timeout', 'itemService', 'routing', 'messenger', 'webStorage', 'data',
      function($controller, $modal, $scope, $timeout, itemService, routing, messenger, webStorage, data) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope:   $scope,
          $timeout: $timeout,
          data:     data
        }));

        /**
         * @memberOf ClientListCtrl
         *
         * @description
         *   The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          name_like: []
        };

        /**
         * @memberOf ClientListCtrl
         *
         * @description
         *   The visible table columns.
         *
         * @type {Object}
         */
        $scope.columns = {
          collapsed: 1,
          selected: [ 'name', 'email', 'address', 'city',
            'state', 'country' ]
        };

        /**
         * @memberOf ClientListCtrl
         *
         * @description
         *   The listing order.
         *
         * @type {Object}
         */
        $scope.orderBy = [{ name: 'id', value: 'asc' }];

        /**
         * @function delete
         * @memberOf ClientListCtrl
         *
         * @description
         *   Confirm delete action.
         *
         * @param {Object client The client to delete.
         */
        $scope.delete = function(client) {
          var modal = $modal.open({
            templateUrl: '/managerws/template/client:modal.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { };
              },
              success: function() {
                return function(m) {
                  itemService.delete('manager_ws_client_delete', client.id)
                    .success(function(response) {
                      m.close({ message: response, type: 'success'});
                    }).error(function(response) {
                      m.close({ message: response, type: 'error'});
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
         * @memberOf ClientListCtrl
         *
         * @description
         *   Confirm delete action.
         */
        $scope.deleteSelected = function() {
          var modal = $modal.open({
            templateUrl: '/managerws/template/client:modal.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { selected: $scope.selected.items.length };
              },
              success: function() {
                return function(modalClient) {
                  itemService.deleteSelected('manager_ws_clients_delete',
                    $scope.selected.items).success(function(response) {
                      modalClient.close(response);
                    }).error(function(response) {
                      modalClient.close(response);
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
         * @function refresh
         * @memberOf ClientListCtrl
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
            epp:      $scope.pagination.epp, // elements per page
            page:     $scope.pagination.page
          };

          itemService.encodeFilters($scope.criteria, $scope.orderBy,
            $scope.pagination.epp, $scope.pagination.page);

          itemService.list('manager_ws_clients_list', data).then(
            function(response) {
              $scope.items = response.data.results;
              $scope.pagination.total = response.data.total;
              $scope.extra = response.data.extra;

              $scope.loading = 0;

              // Scroll top
              $('.page-content').animate({ scrollTop: '0px' }, 1000);
            }
          );
        };

        /**
         * @function resetFilters
         * @memberOf ClientListCtrl
         *
         * @description
         *   Resets all filters to the initial value.
         */
        $scope.resetFilters = function() {
          $scope.criteria   = { name_like: [ { value: '', operator: 'like' } ] };
          $scope.orderBy    = [ { name: 'id', value: 'asc' } ];

          $scope.pagination.page = 1;
        };

        // Updates the columns stored in localStorage.
        $scope.$watch('columns', function(newValues, oldValues) {
          if (newValues !== oldValues) {
            webStorage.local.add('clients-columns', $scope.columns);
          }
        }, true);

        // Initialize filters from URL
        var filters = itemService.decodeFilters();
        for (var name in filters) {
          $scope[name] = filters[name];
        }

        // Get enabled columns from localStorage
        if (webStorage.local.get('clients-columns')) {
          $scope.columns = webStorage.local.get('clients-columns');
        }

        if (webStorage.local.get('token')) {
          $scope.token = webStorage.local.get('token');
        }

        // Prevent dropdown from closing
        $('.dropdown-menu').on('click', '.checkbox,label', function(e) {
          e.stopPropagation();
        });
      }
    ]);
})();

