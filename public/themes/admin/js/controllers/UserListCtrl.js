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
      '$controller', '$location', '$scope', '$uibModal', 'http', 'messenger', 'oqlEncoder', 'webStorage',
      function($controller, $location, $scope, $uibModal, http, messenger, oqlEncoder, webStorage) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', { $scope: $scope }));

        /**
         * @memberOf UserListCtrl
         *
         * @description
         *   The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          epp: 25,
          page: 1,
          orderBy: { name: 'asc' }
        };

        /**
         * @function confirmUser
         * @memberOf UserCtrl
         *
         * @description
         *   Shows a modal to confirm user update.
         */
        $scope.confirm = function(property, value, item) {
          if ($scope.master || !value) {
            if (item) {
              $scope.patch(item, property, value);
              return;
            }

            $scope.patchSelected(property, value);
            return;
          }

          var modal = $uibModal.open({
            templateUrl: 'modal-confirm',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  name: $scope.id ? 'update' : 'create',
                  backend_access: true,
                  value: 1,
                  extra: $scope.data.extra,
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
                $scope.patch(item, property, value);
                return;
              }

              $scope.patchSelected(property, value);
            }
          });
        };

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
                return function(modalWindow) {
                  var route = {
                    name: 'api_v1_backend_user_delete',
                    params: { id: id }
                  };

                  return http.delete(route).then(function(response) {
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
         * @memberOf UserListCtrl
         *
         * @description
         *   Confirm delete action.
         */
        $scope.deleteSelected = function() {
          var modal = $uibModal.open({
            templateUrl: 'modal-delete',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { selected: $scope.selected.items.length };
              },
              success: function() {
                return function(modalWindow) {
                  var route = 'api_v1_backend_users_delete';
                  var data  = { ids: $scope.selected.items };

                  return http.delete(route, data).then(function(response) {
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
         * @memberOf UserListCtrl
         *
         * @description
         *   Reloads the list.
         */
        $scope.list = function() {
          $scope.flags.http.loading = 1;

          oqlEncoder.configure({
            placeholder: {
              name: 'name ~ "[value]" or username ~ "[value]"',
              user_group_id: '([key] = "[value]" and status != 0)',
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
            $scope.items = response.data.items;

            $scope.disableFlags('http');

            // Scroll top
            $('body').animate({ scrollTop: '0px' }, 1000);
          });
        };

        /**
         * @function patch
         * @memberOf UserListCtrl
         *
         * @description
         *   Changes a property for an user.
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
            name: 'api_v1_backend_user_patch',
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
         *   Changes a property for a list of users.
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

          http.patch('api_v1_backend_users_patch', data).then(function(response) {
            $scope.list().then(function() {
              $scope.selected = { all: false, items: [] };
              messenger.post(response.data);
            });
          }, function(response) {
            $scope.list().then(function() {
              $scope.selected = { all: false, items: [] };
              messenger.post(response.data);
            });
          });
        };

        /**
         * @function resetFilters
         * @memberOf UserListCtrl
         *
         * @description
         *   Resets all filters to the initial value.
         */
        $scope.resetFilters = function() {
          $scope.criteria = {
            epp: 25,
            page: 1,
            orderBy: { name: 'asc' }
          };
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
