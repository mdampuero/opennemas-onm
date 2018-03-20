(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name UserListCtrl
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
     *   Handles all actions in users listing.
     */
    .controller('UserListCtrl', [
      '$controller', '$location', '$scope', '$timeout', '$uibModal', 'http', 'messenger', 'oqlEncoder', 'webStorage',
      function($controller, $location, $scope, $timeout, $uibModal, http, messenger, oqlEncoder, webStorage) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope:   $scope,
          $timeout: $timeout
        }));

        /**
         * @memberOf UserListCtrl
         *
         * @description
         *   The visible table columns.
         *
         * @type {Object}
         */
        $scope.columns = {
          collapsed: 1,
          selected:  [ 'name', 'username', 'usergroups', 'enabled' ]
        };

        /**
         * @memberOf UserListCtrl
         *
         * @description
         *   The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = { epp: 25, page: 1 };

        /**
         * @memberOf UserListCtrl
         *
         * @description
         *   Confirm delete action.
         *
         * @param {Integer} id The user id.
         */
        $scope.delete = function(id) {
          var modal = $uibModal.open({
            templateUrl: 'modal-delete',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  content: $scope.items.filter(function(e) {
                    return e.id === id;
                  })[0]
                };
              },
              success: function() {
                return function() {
                  var route = {
                    name: 'backend_ws_user_delete',
                    params: { id: id }
                  };

                  return http.delete(route);
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
            templateUrl: 'modal-delete-selected',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { selected: $scope.selected.items.length };
              },
              success: function() {
                return function() {
                  var route = 'backend_ws_users_delete';
                  var data  = { ids: $scope.selected.items };

                  return http.delete(route, data);
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
         * @function getUserGroup
         * @memberOf UserListCtrl
         *
         * @description
         *   Returns the user group name given its id.
         *
         * @param {Integer} id The user group id.
         *
         * @return {String} The user group name.
         */
        $scope.getUserGroup = function(id) {
          for (var i = 0; i < $scope.extra.user_groups.length; i++) {
            if ($scope.extra.user_groups[i].pk_user_group === parseInt(id)) {
              return $scope.extra.user_groups[i].name;
            }
          }
        };

        /**
         * @function list
         * @memberOf InstanceListCtrl
         *
         * @description
         *   Reloads the list.
         */
        $scope.list = function() {
          $scope.flags.loading = 1;

          oqlEncoder.configure({
            placeholder: {
              name: 'name ~ "[value]" or username ~ "[value]"',
              fk_user_group: '[key] regexp "^[value],|^[value]$|,[value],|,[value]$"'
            }
          });

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: 'api_v1_backend_users_list',
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            $scope.data  = response.data;
            $scope.items = response.data.results;

            $scope.disableFlags();

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
         * @memberOf UserListCtrl
         *
         * @description
         *   Enables/disables an user.
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
            name: 'backend_ws_user_patch',
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
         * @memberOf UserListCtrl
         *
         * @description
         *   Enables/disables the selected users.
         *
         * @param {String}  property The property name.
         * @param {Boolean} value    The property value.
         */
        $scope.patchSelected = function(property, value) {
          // Calculate backend access
          var backend_access = false;
          var selected = $scope.items.filter(function(e) {
            return $scope.selected.items.indexOf(e.id) !== -1;
          });

          var i = 0;

          while (i < selected.length && !backend_access) {
            if (selected[i++].type === 0 && value === 1) {
              backend_access = true;
            }
          }

          var modal = $uibModal.open({
            templateUrl: 'modal-update-selected',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  checkPhone:  $scope.checkPhone,
                  checkVat:    $scope.checkVat,
                  extra:       $scope.extra,
                  name:        property,
                  selected:    $scope.selected,
                  value:       value,
                  backend_access: backend_access
                };
              },
              success: function() {
                return function(modalWindow) {
                  for (var i = 0; i < $scope.items.length; i++) {
                    var id = $scope.items[i].id;

                    if ($scope.selected.items.indexOf(id) !== -1) {
                      $scope.items[i][property + 'Loading'] = 1;
                    }
                  }

                  var data = { ids: $scope.selected.items };

                  data[property] = value;

                  return http.patch('backend_ws_users_patch', data);
                };
              }
            }
          });

          modal.result.then(function(response) {
            $scope.selected = { all: false, items: [] };
            messenger.post(response.data);
            $scope.list();
          });
        };

        // Updates the columns stored in localStorage.
        $scope.$watch('columns', function(nv, ov) {
          if (nv !== ov) {
            webStorage.local.set('users-columns', nv);
          }
        }, true);

        // Get enabled columns from localStorage
        if (webStorage.local.get('users-columns')) {
          $scope.columns = webStorage.local.get('users-columns');
        }
      }
    ]);
})();
