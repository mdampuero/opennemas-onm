(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  RestListCtrl
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
     *   Handles all actions in list.
     */
    .controller('RestListCtrl', [
      '$controller', '$location', '$scope', '$uibModal', 'http', 'messenger', 'oqlEncoder', 'webStorage',
      function($controller, $location, $scope, $uibModal, http, messenger, oqlEncoder, webStorage) {
        $.extend(this, $controller('ListCtrl', { $scope: $scope }));

        /**
         * @memberOf RestListCtrl
         *
         * @description
         *   The visible table columns.
         *
         * @type {Object}
         */
        $scope.columns = {
          key: 'columns',
          collapsed: 1,
          selected:  [ 'name' ]
        };

        /**
         * @memberOf RestListCtrl
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
         * @function delete
         * @memberOf RestListCtrl
         *
         * @description
         *   Confirm delete action.
         *
         * @param {Integer} id The group id.
         */
        $scope.delete = function(id) {
          var modal = $uibModal.open({
            templateUrl: 'modal-delete',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return null;
              },
              success: function() {
                return function() {
                  var route = {
                    name: $scope.routes.delete,
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
         * @memberOf RestListCtrl
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
                  var route = $scope.routes.deleteSelected;
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
         * @function getId
         * @memberOf RestListCtrl
         *
         * @description
         *   Returns the item id.
         *
         * @param {Object} item The item.
         *
         * @return {Integer} The item id.
         */
        $scope.getId = function(item) {
          return item.id;
        };

        /**
         * @function isSelectable
         * @memberOf RestListCtrl
         *
         * @description
         *   Checks if the item is selectable.
         *
         * @param {Object} item The item to check.
         *
         * @return {Boolean} True if the item is selectable. False otherwise.
         */
        $scope.isSelectable = function() {
          return true;
        };

        /**
         * @function list
         * @memberOf RestListCtrl
         *
         * @description
         *   Reloads the list.
         */
        $scope.list = function() {
          $scope.flags.http.loading = 1;

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: $scope.routes.list,
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            response.data = $scope.parseList(response.data);

            $scope.data  = response.data;
            $scope.items = response.data.items;

            $scope.disableFlags('http');

            // Scroll top
            $('body').animate({ scrollTop: '0px' }, 1000);
          });
        };

        /**
         * @function patch
         * @memberOf RestListCtrl
         *
         * @description
         *   Enables/disables an item.
         *
         * @param {String} item     The item.
         * @param {String} property The property name.
         * @param {Mixed} value     The property value.
         */
        $scope.patch = function(item, property, value) {
          var data = {};

          item[property + 'Loading'] = 1;
          data[property] = value;

          var route = {
            name:   $scope.routes.patch,
            params: { id: $scope.getId(item) }
          };

          http.patch(route, data).then(function(response) {
            item[property + 'Loading'] = 0;
            item[property] = value;
            messenger.post(response.data);
          }, function(response) {
            delete item[property + 'Loading'];
            messenger.post(response.data);
          });
        };

        /**
         * @function patchSelected
         * @memberOf RestListCtrl
         *
         * @description
         *   description
         *
         * @param {String}  property The property name.
         * @param {Integer} value    The property value.
         */
        $scope.patchSelected = function(property, value) {
          for (var i = 0; i < $scope.items.length; i++) {
            var id = $scope.getId($scope.items[i]);

            if ($scope.selected.items.indexOf(id) !== -1) {
              $scope.items[i][property + 'Loading'] = 1;
            }
          }

          var data = { ids: $scope.selected.items };

          data[property] = value;

          http.patch($scope.routes.patchSelected, data)
            .then(function(response) {
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
         * @function parseList
         * @memberOf RestListCtrl
         *
         * @description
         *   Parses the response and adds information to the scope.
         *
         * @param {Object} data The data in the response.
         */
        $scope.parseList = function(data) {
          return data;
        };

        /**
         * @function resetFilters
         * @memberOf RestListCtrl
         *
         * @description
         *   Resets all filters to the initial value.
         */
        $scope.resetFilters = function() {
          $scope.criteria = $scope.backup.criteria;
        };

        /**
         * @function toggleAll
         * @memberOf RestListCtrl
         *
         * @description
         *   Toggles all items selection.
         */
        $scope.toggleAll = function() {
          if ($scope.selected.all) {
            $scope.selected.items = $scope.items.filter(function(item) {
              return $scope.isSelectable(item);
            }).map(function(item) {
              return $scope.getId(item);
            });
          } else {
            $scope.selected.items = [];
          }
        };

        // Updates the columns stored in localStorage.
        $scope.$watch('columns', function(nv, ov) {
          if (nv !== ov) {
            webStorage.local.set($scope.columns.key, nv);
          }
        }, true);

        // Get enabled columns from localStorage
        if (webStorage.local.get($scope.columns.key)) {
          $scope.columns = webStorage.local.get($scope.columns.key);
        }
      }
    ]);
})();
