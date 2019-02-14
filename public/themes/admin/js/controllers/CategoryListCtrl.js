(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  CategoryListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Handles all actions in category list.
     */
    .controller('CategoryListCtrl', [
      '$controller', '$scope', '$timeout', '$uibModal', 'http', 'messenger', 'oqlEncoder',
      function($controller, $scope, $timeout, $uibModal, http, messenger, oqlEncoder) {
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
          delete:         'api_v1_backend_category_delete',
          deleteSelected: 'api_v1_backend_categories_delete',
          empty:          'api_v1_backend_category_empty',
          emptySelected:  'api_v1_backend_categories_empty',
          list:           'api_v1_backend_categories_list',
          move:           'api_v1_backend_category_move',
          moveSelected:   'api_v1_backend_categories_move',
          patch:          'api_v1_backend_category_patch',
          patchRss:       'api_v1_backend_category_update',
          patchSelected:  'api_v1_backend_categories_patch',
          save:           'api_v1_backend_category_save',
          update:         'api_v1_backend_category_update',
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
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {};
              },
              success: function() {
                return function() {
                  var route = {
                    name: $scope.routes.empty,
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
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  selected: $scope.selected.items.length
                };
              },
              success: function() {
                return function() {
                  return http.put($scope.routes.emptySelected, {
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
         * @inheritdoc
         */
        $scope.getId = function(item) {
          return item.pk_content_category;
        };

        /**
         * @function init
         * @memberOf CategoryListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function(locale) {
          $scope.locale          = locale;
          $scope.columns.key     = 'category-columns';
          $scope.backup.criteria = $scope.criteria;

          $scope.criteria.orderBy = { name: 'asc' };

          $scope.criteria.epp = null;

          oqlEncoder.configure({ placeholder: {
            internal_category: '[key] = [value] and [key] != 0',
            name: '[key] ~ "[value]"',
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
          return !$scope.data.extra.stats[$scope.getId(item)] ||
            $scope.data.extra.stats[$scope.getId(item)] === 0;
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
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  source: item,
                  categories: angular.copy($scope.items).filter(function(e) {
                    return $scope.getId(e) !== id;
                  })
                };
              },
              success: function() {
                return function(modal, template) {
                  var route = {
                    name: $scope.routes.move,
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
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  selected: $scope.selected.items.length,
                  categories: angular.copy($scope.items).filter(function(e) {
                    return $scope.selected.items.indexOf($scope.getId(e)) === -1;
                  })
                };
              },
              success: function() {
                return function(modal, template) {
                  return http.put($scope.routes.moveSelected, {
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
          if (data.extra.locale) {
            $scope.config.locale = data.extra.locale;
          }

          $scope.data.items = $scope.sortItems($scope.data.items, 0, 0);

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
            return e.fk_content_category === parent;
          });

          var sorted = [];

          for (var i = 0; i < parents.length; i++) {
            sorted.push(parents[i]);
            $scope.levels[$scope.getId(parents[i])] = level;

            var children = $scope.sortItems(items,
              $scope.getId(parents[i]), level + 1);

            if (children.length > 0) {
              sorted = sorted.concat(children);
            }
          }

          return sorted;
        };

        /**
         * @function patchRss
         * @memberOf RestInnerCtrl
         *
         * @description
         *   Update the inrss value for an item.
         */
        $scope.patchRss = function(item, value) {
          item.inrssLoading = true;

          var data  = angular.copy(item);
          var route = {
            name: $scope.routes.patchRss,
            params: { id: $scope.getId(item) }
          };

          data.params.inrss = value;

          http.put(route, data).then(function(response) {
            messenger.post(response.data);

            item.params.inrss = value;
            item.inrssLoading = false;
          }, $scope.errorCb);
        };
      }
    ]);
})();
