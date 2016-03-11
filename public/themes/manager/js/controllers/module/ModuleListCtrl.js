(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  ModuleListCtrl
     *
     * @requires $controller
     * @requires $uibModal
     * @requires $scope
     * @requires itemService
     * @requires routing
     * @requires messenger
     * @requires webStorage
     * @requires data
     *
     * @description
     *   Handles all actions in modules listing.
     */
    .controller('ModuleListCtrl', [
      '$controller', '$uibModal', '$scope', '$timeout', 'itemService', 'routing', 'messenger', 'webStorage',
      function($controller, $uibModal, $scope, $timeout, itemService, routing, messenger, webStorage) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope:   $scope,
          $timeout: $timeout
        }));

        /**
         * @memberOf ModuleListCtrl
         *
         * @description
         *   The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = { name_like: [] };

        /**
         * @memberOf ModuleListCtrl
         *
         * @description
         *   The visible table columns.
         *
         * @type {Object}
         */
        $scope.columns = {
          collapsed: 1,
          selected: [ 'enabled', 'created', 'image', 'l10n', 'name', 'updated',
            'uuid' ]
        };

        /**
         * @memberOf ModuleListCtrl
         *
         * @description
         *   The listing order.
         *
         * @type {Object}
         */
        $scope.orderBy = [{ name: 'id', value: 'asc' }];

        /**
         * @function countStringsLeft
         * @memberOf ModuleListCtrl
         *
         * @description
         *   Counts the number of remaining strings for a language.
         *
         * @param {Object} item The item to check.
         *
         * @return {Integer} The number of remaining strings.
         */
        $scope.countStringsLeft = function(item) {
          var left = 0;

          for (var lang in $scope.extra.languages) {
            if (!item.name || !item.name[lang]) {
              left++;
            }

            if (!item.description || !item.description[lang]) {
              left++;
            }

            if (!item.about || !item.about[lang]) {
              left++;
            }
          }

          return left;
        };

        /**
         * @function delete
         * @memberOf ModuleListCtrl
         *
         * @description
         *   Confirm delete action.
         *
         * @param {Object module The module to delete.
         */
        $scope.delete = function(module) {
          var modal = $uibModal.open({
            templateUrl: 'modal-confirm',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  name: 'delete-module',
                  item: module
                };
              },
              success: function() {
                return function(modalModule) {
                  itemService.delete('manager_ws_module_delete', module.id)
                    .success(function(response) {
                      modalModule.close({ message: response, type: 'success'});
                    }).error(function(response) {
                      modalModule.close({ message: response, type: 'error'});
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
         * @memberOf ModuleListCtrl
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
                  name: 'delete-modules',
                  selected: selected
                };
              },
              success: function() {
                return function(modalModule) {
                  itemService.deleteSelected('manager_ws_modules_delete',
                    $scope.selected.items).success(function(response) {
                      modalModule.close(response);
                    }).error(function(response) {
                      modalModule.close(response);
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
         * @memberOf ModuleListCtrl
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

          itemService.list('manager_ws_modules_list', data).then(
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
         * @function setEnabled
         * @memberOf ModuleListCtrl
         *
         * @description
         *   Enables/disables an module.
         *
         * @param boolean enabled Module enabled value.
         */
        $scope.setEnabled = function(module, enabled) {
          module.loading = 1;

          itemService.patch('manager_ws_module_patch', module.id,
            { enabled: enabled }).success(function(response) {
              module.loading = 0;
              module.enabled = enabled;

              messenger.post({ message: response, type: 'success' });
            }).error(function(response) {
              messenger.post({ message: response, type: 'error' });
            });
        };

        /**
         * @function setEnabledSelected
         * @memberOf ModuleListCtrl
         *
         * @description
         *   Enables/disables the selected modules.
         *
         * @param integer enabled The enabled value.
         */
        $scope.setEnabledSelected = function(enabled) {
          for (var i = 0; i < $scope.items.length; i++) {
            var id = $scope.items[i].id;
            if ($scope.selected.items.indexOf(id) !== -1) {
              $scope.items[i].loading = 1;
            }
          }

          var data = { selected: $scope.selected.items, enabled: enabled };

          itemService.patchSelected('manager_ws_modules_patch', data)
            .success(function(response) {
              // Update modules changed successfully
              for (var i = 0; i < $scope.items.length; i++) {
                var id = $scope.items[i].id;

                if (response.success.indexOf(id) !== -1) {
                  $scope.items[i].enabled = enabled;
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
              // Update modules changed successfully
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
            webStorage.local.set('modules-columns', $scope.columns);
          }
        }, true);

        // Initialize filters from URL
        var filters = itemService.decodeFilters();
        for (var name in filters) {
          $scope[name] = filters[name];
        }

        // Get enabled columns from localStorage
        if (webStorage.local.get('modules-columns')) {
          $scope.columns = webStorage.local.get('modules-columns');
        }

        $scope.list();
      }
    ]);
})();

