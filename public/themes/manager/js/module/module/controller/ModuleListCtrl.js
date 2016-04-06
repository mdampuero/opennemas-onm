(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  ModuleListCtrl
     *
     * @requires $controller
     * @requires $uibModal
     * @requires $location
     * @requires $scope
     * @requires $timeout
     * @requires http
     * @requires messenger
     * @requires oqlBuilder
     * @requires webStorage
     *
     * @description
     *   Handles all actions in modules listing.
     */
    .controller('ModuleListCtrl', [
      '$controller', '$uibModal', '$location', '$scope', '$timeout', 'http', 'messenger', 'oqlBuilder', 'webStorage',
      function($controller, $uibModal, $location, $scope, $timeout, http, messenger, oqlBuilder, webStorage) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope:   $scope,
          $timeout: $timeout
        }));

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
         *   The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = { epp: 25, page: 1 };

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
         * @param {Object} id The module id.
         */
        $scope.delete = function(id) {
          var modal = $uibModal.open({
            templateUrl: '/managerws/template/module:modal.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { };
              },
              success: function() {
                return function(modalWindow) {
                  var route = {
                    name: 'manager_ws_module_delete',
                    params: { id: id }
                  };

                  http.delete(route).then(function(response) {
                    modalWindow.close({ data: response.data, success: true });
                  }, function(response) {
                    modalWindow.close({ data: response.data, success: false });
                  });
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.list();
            }
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
            templateUrl: '/managerws/template/module:modal.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { selected: $scope.selected.items.length };
              },
              success: function() {
                return function(modalWindow) {
                  var route = 'manager_ws_modules_delete';
                  var data  = { ids: $scope.selected.items };

                  http.delete(route, items).then(function(response) {
                    modalWindow.close({ data: response.data, success: true });
                  }).error(function(response) {
                    modalWindow.close({ data: response.data, success: false });
                  });
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.selected = { all: false, items: [] };
              $scope.list();
            }
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

          oqlBuilder.configure({
            placeholder: { uuid: 'uuid ~ "[value]" or name ~ "[value]"' }
          });

          var oql   = oqlBuilder.getOql($scope.criteria);
          var route = {
            name: 'manager_ws_modules_list',
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            $scope.loading = 0;
            $scope.items   = response.data.results;
            $scope.total   = response.data.total;
            $scope.extra   = response.data.extra;

            // Scroll top
            $('body').animate({ scrollTop: '0px' }, 1000);
          });
        };

        /**
         * @function resetFilters
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Resets all filters to the initial value.
         */
        $scope.resetFilters = function() {
          $scope.criteria = { epp: 25, page: 1 };
        };

        /**
         * @function patch
         * @memberOf ModuleListCtrl
         *
         * @description
         *   Enables/disables a module.
         *
         * @param {String}  notification The notification object.
         * @param {String}  property     The property name.
         * @param {Boolean} value        The property value.
         */
        $scope.patch = function(item, property, value) {
          var data = {};

          item[property + 'Loading'] = 1;
          data[property] = value;

          var route = {
            name: 'manager_ws_module_patch',
            params: { id: item.id }
          };

          http.patch(route, data).then(function(response) {
            item[property + 'Loading'] = 0;
            item[property] = value;
            messenger.post(response.data);
          }, function(response) {
            item[property + 'Loading'] = 0;
            messenger.post(response.data);
          });
        };

        /**
         * @function patchSelected
         * @memberOf ModuleListCtrl
         *
         * @description
         *   Enables/disables the selected modules.
         *
         * @param {String}  property The property name.
         * @param {Boolean} value    The property value.
         */
        $scope.patchSelected = function(property, value) {
          for (var i = 0; i < $scope.items.length; i++) {
            var id = $scope.items[i].id;
            if ($scope.selected.items.indexOf(id) !== -1) {
              $scope.items[i][property + 'Loading'] = 1;
            }
          }

          var data = { ids: $scope.selected.items };
          data[property] = value;

          http.patch('manager_ws_modules_patch', data)
            .then(function(response) {
              $scope.list().then(function() {
                messenger.post(response.data);
                $scope.selected = { all: false, items: [] };
              });
            }, function(response) {
              $scope.list().then(function() {
                messenger.post(response.data);
                $scope.selected = { all: false, items: [] };
              });
            });
        };

        // Updates the columns stored in localStorage.
        $scope.$watch('columns', function(newValues, oldValues) {
          if (newValues !== oldValues) {
            webStorage.local.add('modules-columns', $scope.columns);
          }
        }, true);

        // Get enabled columns from localStorage
        if (webStorage.local.get('modules-columns')) {
          $scope.columns = webStorage.local.get('modules-columns');
        }

        $scope.list();
      }
    ]);
})();

