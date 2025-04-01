(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  CategoryListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $uibModal
     * @requires http
     * @requires messenger
     * @requires oqlEncoder
     *
     * @description
     *   Handles all actions in category list.
     */
    .controller('CategoryListCtrl', [
      '$controller', '$scope', '$uibModal', 'http', 'messenger', 'oqlEncoder',
      function($controller, $scope, $uibModal, http, messenger, oqlEncoder) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * A map where the key is the category id and the value is the depth
         * level.
         *
         * @type {Array}
         */
        $scope.levels = [];

        /**
         * @memberOf CategoryListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_category_delete_item',
          deleteList: 'api_v1_backend_category_delete_list',
          emptyItem:  'api_v1_backend_category_empty_item',
          emptyList:  'api_v1_backend_category_empty_list',
          getList:    'api_v1_backend_category_get_list',
          moveItem:   'api_v1_backend_category_move_item',
          moveList:   'api_v1_backend_category_move_list',
          patchItem:  'api_v1_backend_category_patch_item',
          patchList:  'api_v1_backend_category_patch_list',
          updateItem: 'api_v1_backend_category_update_item',
        };

        /**
         * @function areSelectedEmpty
         * @memberOf CategoryListCtrl
         *
         * @description
         *   Checks if all selected categories are empty.
         *
         * @return {Boolean} True if all selected categories are empty. False
         *                   otherwise.
         */
        $scope.areSelectedEmpty = function() {
          if (!$scope.selected.items) {
            return false;
          }

          var notEmpty = $scope.selected.items.filter(function(e) {
            return $scope.data.extra.stats[e] &&
              $scope.data.extra.stats[e] > 0;
          });

          return notEmpty.length === 0;
        };

        /**
         * @function areSelectedNotEmpty
         * @memberOf CategoryListCtrl
         *
         * @description
         *   Checks if all selected categories are empty.
         *
         * @return {Boolean} True if all selected categories are not empty.
         *                   False otherwise.
         */
        $scope.areSelectedNotEmpty = function() {
          if (!$scope.selected.items) {
            return false;
          }

          var empty = $scope.selected.items.filter(function(e) {
            return !$scope.data.extra.stats[e] ||
              $scope.data.extra.stats[e] === 0;
          });

          return empty.length === 0;
        };

        /**
         * @function empty
         * @memberOf RestListCtrl
         *
         * @description
         *   Confirm empty action.
         *
         * @param {Integer} id The category id.
         */
        $scope.empty = function(id) {
          var modal = $uibModal.open({
            templateUrl: 'modal-empty',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {};
              },
              success: function() {
                return function() {
                  var route = {
                    name: $scope.routes.emptyItem,
                    params: { id: id }
                  };

                  return http.put(route);
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.list().then(function() {
                $scope.selected = { all: false, items: [] };
              });
            }
          });
        };

        /**
         * @function emptySelected
         * @memberOf RestListCtrl
         *
         * @description
         *   Confirm empty action for a list of items.
         */
        $scope.emptySelected = function() {
          var modal = $uibModal.open({
            templateUrl: 'modal-empty',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {
                  selected: $scope.selected.items.length
                };
              },
              success: function() {
                return function() {
                  return http.put($scope.routes.emptyList, {
                    ids: $scope.selected.items
                  });
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.list().then(function() {
                $scope.selected = { all: false, items: [] };
              });
            }
          });
        };

        /**
         * @function init
         * @memberOf CategoryListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria = $scope.criteria;

          $scope.app.columns.selected =  _.uniq($scope.app.columns.selected.concat(
            [ 'name', 'slug', 'contents', 'cover', 'color', 'visibility', 'enabled', 'rss' ]
          ));
          $scope.criteria.orderBy = { name: 'asc' };
          $scope.criteria.epp     = 10;

          oqlEncoder.configure({ placeholder: {
            name: 'name ~ "%[value]%" or title ~ "%[value]%"',
          } });

          $scope.list();
        };

        /**
         * @function isEmpty
         * @memberOf CategoryListCtrl
         *
         * @description
         *   Checks if the item is empty.
         *
         * @param {Object} item The item to check.
         *
         * @return {Boolean} True if all selected categories are empty. False
         *                   otherwise.
         */
        $scope.isEmpty = function(item) {
          return !$scope.data.extra.stats[$scope.getItemId(item)] ||
            $scope.data.extra.stats[$scope.getItemId(item)] === 0;
        };

        /**
         * @function move
         * @memberOf RestListCtrl
         *
         * @description
         *   Confirm move action.
         *
         * @param {Integer} id       The category id.
         * @param {Object}  category The category object.
         */
        $scope.move = function(id, item) {
          var modal = $uibModal.open({
            templateUrl: 'modal-move',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {
                  exclude: [ id ],
                  source: item
                };
              },
              success: function() {
                return function(modal, template) {
                  var route = {
                    name: $scope.routes.moveItem,
                    params: { id: id }
                  };

                  return http.put(route, { target: template.target });
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.list().then(function() {
                $scope.selected = { all: false, items: [] };
              });
            }
          });
        };

        /**
         * @function moveSelected
         * @memberOf RestListCtrl
         *
         * @description
         *   Confirm move action.
         */
        $scope.moveSelected = function() {
          var modal = $uibModal.open({
            templateUrl: 'modal-move',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {
                  exclude: $scope.selected.items,
                  selected: $scope.selected.items.length
                };
              },
              success: function() {
                return function(modal, template) {
                  return http.put($scope.routes.moveList, {
                    ids: $scope.selected.items, target: template.target
                  });
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.list().then(function() {
                $scope.selected = { all: false, items: [] };
              });
            }
          });
        };

        /**
         * @inheritdoc
         */
        $scope.parseList = function(data) {
          var items = $scope.data.items;

          $scope.data.items = $scope.sortItems($scope.data.items, null, 0);

          if (items.length !== $scope.data.items.length) {
            $scope.data.items = items;
          }

          $scope.configure(data.extra);
          $scope.localize($scope.data.items, 'items');
        };

        /**
         * @function sortItems
         * @memberOf CategoryListCtrl
         *
         * @description
         *   Sorts items basing on the parent id.
         *
         * @param {Array}   items  The list of items to sort.
         * @param {Integer} parent The id of the item to sort children for.
         * @param {Integer} level  The level of the current children.
         *
         * @return {Array} The list of items sorted by parent.
         */
        $scope.sortItems = function(items, parent, level) {
          var parents = items.filter(function(e) {
            return e.parent_id === parent;
          });

          var sorted = [];

          for (var i = 0; i < parents.length; i++) {
            sorted.push(parents[i]);
            $scope.levels[$scope.getItemId(parents[i])] = level;

            var children = $scope.sortItems(items,
              $scope.getItemId(parents[i]), level + 1);

            if (children.length > 0) {
              sorted = sorted.concat(children);
            }
          }

          return sorted;
        };
      }
    ]);
})();
