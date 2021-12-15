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
     *
     * @description
     *   Handles all actions in list.
     */
    .controller('RestListCtrl', [
      '$controller', '$location', '$scope', '$uibModal', 'http', 'messenger', 'oqlEncoder',
      function($controller, $location, $scope, $uibModal, http, messenger, oqlEncoder) {
        $.extend(this, $controller('ListCtrl', { $scope: $scope }));

        /**
         * @memberOf RestListCtrl
         *
         * @description
         *   Always collapse the list of columns
         *
         */
        $scope.app.columns.collapsed = true;

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
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return null;
              },
              success: function() {
                return function() {
                  var route = {
                    name: $scope.routes.deleteItem,
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
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return { selected: $scope.selected.items.length };
              },
              success: function() {
                return function() {
                  var route = $scope.routes.deleteList;
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
         * @function getExportUrl
         * @memberOf RestListCtrl
         *
         * @description
         *   Generates the URL to export items to a CSV file.
         *
         * @return {String} The URL to export items to a CSV file.
         */
        $scope.getExportUrl = function() {
          var criteria = angular.copy($scope.criteria);

          if (!criteria) {
            return '';
          }

          return $scope.routing.generate($scope.routes.getList, {
            format: '.csv',
            oql: oqlEncoder.getOql(criteria)
          });
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
            name: $scope.routes.getList,
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            $scope.data = response.data;

            if (!response.data.items) {
              $scope.data.items = [];
            }

            $scope.items = $scope.data.items;

            $scope.parseList(response.data);
            $scope.disableFlags('http');
          }, function(response) {
            messenger.post(response.data);
            $scope.disableFlags('http');
            $scope.items = [];
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
            name:   $scope.routes.patchItem,
            params: { id: $scope.getItemId(item) }
          };

          return http.patch(route, data).then(function(response) {
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
            var id = $scope.getItemId($scope.items[i]);

            if ($scope.selected.items.indexOf(id) !== -1) {
              $scope.items[i][property + 'Loading'] = 1;
            }
          }

          var data = { ids: $scope.selected.items };

          data[property] = value;

          return http.patch($scope.routes.patchList, data)
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
      }
    ]);
})();
