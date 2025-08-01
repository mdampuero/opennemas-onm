(function() {
  'use strict';

  angular.module('ManagerApp.controllers')

    /**
     * @ngdoc controller
     * @name  UserGroupListCtrl
     *
     * @requires $controller
     * @requires $location
     * @requires $scope
     * @requires $timeout
     * @requires $uibModal
     * @requires http
     * @requires messenger
     * @requires oqlEncoder
     * @requires webStorage
     *
     * @description
     *   Handles all actions in user groups list.
     */
    .controller('UserGroupListCtrl', [
      '$controller', '$location', '$scope', '$timeout', '$uibModal', 'http',
      'messenger', 'oqlDecoder', 'oqlEncoder', 'webStorage',
      function($controller, $location, $scope, $timeout, $uibModal, http,
          messenger, oqlDecoder, oqlEncoder, webStorage) {
        $.extend(this, $controller('ListCtrl', { $scope: $scope }));

        /**
         * @memberOf UserGroupListCtrl
         *
         * @description
         *   The visible table columns.
         *
         * @type {Object}
         */
        $scope.columns = {
          collapsed: 1,
          selected:  [ 'name' ]
        };

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
         * @param {Integer} id The group id.
         */
        $scope.delete = function(id) {
          var modal = $uibModal.open({
            templateUrl: '/managerws/template/user_group:modal.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { };
              },
              success: function() {
                return function(modalWindow) {
                  var route = {
                    name: 'manager_ws_user_group_delete',
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
         * @memberOf InstanceListCtrl
         *
         * @description
         *   Confirm delete action.
         */
        $scope.deleteSelected = function() {
          var modal = $uibModal.open({
            templateUrl: '/managerws/template/user_group:modal.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { selected: $scope.selected.items.length };
              },
              success: function() {
                return function(modalWindow) {
                  var route = 'manager_ws_user_groups_delete';
                  var data  = { ids: $scope.selected.items };

                  http.delete(route, data).then(function(response) {
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
              $scope.selected = { all: false, items: [] };
              $scope.list();
            }
          });
        };

        /**
         * @function list
         * @memberOf UserGroupListCtrl
         *
         * @description
         *   Reloads the list.
         */
        $scope.list = function() {
          $scope.loading = 1;

          oqlEncoder.configure({ placeholder: { name: '[key] ~ "[value]"' } });

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: 'manager_ws_user_groups_list',
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
         * @function patch
         * @memberOf UserGroupListCtrl
         *
         * @description
         *   Enables/disables an user group.
         *
         * @param {String}  item     The user object.
         * @param {String}  property The property name.
         * @param {Boolean} value    The property value.
         */
        $scope.patch = function(item, property, value) {
          var data = {};

          item[property + 'Loading'] = 1;
          data[property] = value;

          var route = {
            name: 'manager_ws_user_group_patch',
            params: { id: item.pk_user_group }
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
         * @memberOf UserListCtrl
         *
         * @description
         *   Enables/disables the selected users.
         *
         * @param {String}  property The property name.
         * @param {Boolean} value    The property value.
         */
        $scope.patchSelected = function(property, value) {
          for (var i = 0; i < $scope.items.length; i++) {
            var id = $scope.items[i].pk_user_group;

            if ($scope.selected.items.indexOf(id) !== -1) {
              $scope.items[i][property + 'Loading'] = 1;
            }
          }

          var data = { ids: $scope.selected.items };

          data[property] = value;

          http.patch('manager_ws_user_groups_patch', data).then(function(response) {
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
         * @function toggleAll
         * @memberOf ListCtrl
         *
         * @description
         *   Toggles all items selection.
         */
        $scope.toggleAll = function() {
          if ($scope.selected.all) {
            $scope.selected.items = $scope.items.map(function(item) {
              return item.pk_user_group;
            });
          } else {
            $scope.selected.items = [];
          }
        };

        // Updates the columns stored in localStorage.
        $scope.$watch('columns', function(nv, ov) {
          if (nv !== ov) {
            webStorage.local.set('user-groups-columns', nv);
          }
        }, true);

        // Get enabled columns from localStorage
        if (webStorage.local.get('user-groups-columns')) {
          $scope.columns = webStorage.local.get('user-groups-columns');
        }

        if ($location.search().oql) {
          $scope.criteria = oqlDecoder.decode($location.search().oql);
        }

        $scope.list();
      }
    ]);
})();
