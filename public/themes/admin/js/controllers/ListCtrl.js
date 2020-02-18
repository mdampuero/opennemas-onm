(function() {
  'use strict';

  angular.module('BackendApp')

    /**
     * @ngdoc controller
     * @name  ListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $timeout
     *
     * @description
     *   Generic controller for lists.
     */
    .controller('ListCtrl', [
      '$controller', '$scope', '$uibModal', 'http', 'messenger', '$timeout',
      function($controller, $scope, $uibModal, http, messenger, $timeout) {
        $.extend(this, $controller('BaseCtrl', { $scope: $scope }));

        /**
         * @memberOf ListCtrl
         *
         * @description
         *   The list of selected elements.
         *
         * @type {Array}
         */
        $scope.selected = { all: false, items: [] };

        /**
         * @memberOf ListCtrl
         *
         * @description
         *  Variable for timeout actions.
         *
         * @type {type}
         */
        $scope.tm = null;

        /**
         * @function areAllSelected
         * @memberOf ListCtrl
         *
         * @description
         *   Checks if all items are already selected.
         *
         * @return {Boolean} True if all items are selected. False otherwise.
         */
        $scope.areAllSelected = function() {
          return $scope.items && $scope.items.filter(function(e) {
            return $scope.isSelectable(e);
          }).length === $scope.selected.items.length;
        };

        /**
         * Checks if any item can be selected.
         *
         * @return {Boolean} True if one or more items can be selected. False
         *                   otherwise.
         */
        $scope.areSelectable = function() {
          if (!$scope.items || $scope.items.length === 0) {
            return false;
          }

          for (var i = 0; i < $scope.items.length; i++) {
            if ($scope.isSelectable($scope.items[i])) {
              return true;
            }
          }

          return false;
        };

        /**
         * @function clear
         * @memberOf ListCtrl
         *
         * @description
         *   Deletes a value from criteria.
         *
         * @param {String} property The property name.
         */
        $scope.clear = function(property) {
          delete $scope.criteria[property];
        };

        /**
         * @function countSelectedItems
         * @memberOf ListCtrl
         *
         * @description
         *   Returns the number of selected items.
         */
        $scope.countSelectedItems = function() {
          return $scope.selected.items ? $scope.selected.items.length : 0;
        };

        /**
         * @function deselectAll
         * @memberOf ListCtrl
         *
         * @description
         *   Deselects all elements.
         */
        $scope.deselectAll = function() {
          $scope.selected = { all: false, items: [] };
        };

        /**
         * @function getItemId
         * @memberOf ListCtrl
         *
         * @description
         *   Returns the item id.
         *
         * @param {Object} item The item.
         *
         * @return {Integer} The item id.
         */
        $scope.getItemId = function(item) {
          return item.id;
        };

        /**
         * @function isColumnEnabled
         * @memberOf ListCtrl
         *
         * @description
         *   Checks if a columns is enabled.
         *
         * @param {String} name The columns name.
         */
        $scope.isColumnEnabled = function(name) {
          return !$scope.isColumnHidden(name) &&
            $scope.app.columns.selected.indexOf(name) !== -1;
        };

        /**
         * @function isColumnHidden
         * @memberOf ListCtrl
         *
         * @description
         *   Checks if a columns is hidden for the current list.
         *
         * @param {String} name The columns name.
         */
        $scope.isColumnHidden = function(name) {
          return $scope.app.columns.hidden.indexOf(name) !== -1;
        };

        /**
         * @function isOrderedBy
         * @memberOf ListCtrl
         *
         * @description
         *   Checks if the list is ordered by the given field name.
         *
         * @param {String} name The field name.
         *
         * @return {mixed} The order value, if the order exists. Otherwise,
         *                 returns false.
         */
        $scope.isOrderedBy = function(name) {
          if ($scope.criteria && $scope.criteria.orderBy &&
              $scope.criteria.orderBy[name]) {
            return $scope.criteria.orderBy[name];
          }

          return false;
        };

        /**
         * @function isSelectable
         * @memberOf ListCtrl
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
         * @function isSelected
         * @memberOf ListCtrl
         *
         * @description
         *   Checks if an item is selected.
         *
         * @param {String} id The item id.
         */
        $scope.isSelected = function(id) {
          return $scope.selected.items.indexOf(id) !== -1;
        };

        /**
         * @function list
         * @memberOf ListCtrl
         *
         * @description
         *   Just a dummy actions that forces the developer
         *   to overwrite this method on child classes.
         */
        $scope.list = function() {
          throw Error('Method not implemented');
        };

        /**
         * @function resetFilters
         * @memberOf ListCtrl
         *
         * @description
         *   Resets all filters to the initial value.
         */
        $scope.resetFilters = function() {
          $scope.criteria = $scope.backup.criteria;
        };

        /**
         * @function scroll
         * @memberOf ListCtrl
         *
         * @description
         *   Increases page one by one.
         */
        $scope.scroll = function() {
          if ($scope.data.total === $scope.items.length) {
            return;
          }

          $scope.criteria.page++;
        };

        /**
         * @function searchByKeypress
         * @memberOf ListCtrl
         *
         * @description
         *   Reloads the list on keypress.
         *
         * @param {Object} event The event object.
         */
        $scope.searchByKeypress = function(event) {
          if (event.keyCode === 13) {
            if ($scope.criteria.page !== 1) {
              $scope.criteria.page = 1;
              return;
            }

            $scope.list();
          }
        };

        $scope.select = function(item) {
          $scope.selected.lastSelected = item;
        };

        /**
         * @function sort
         * @memberOf InstanceListCtrl
         *
         * @description
         *   Changes the sort order.
         *
         * @param string name Field name.
         */
        $scope.sort = function(name) {
          if (!$scope.criteria.orderBy) {
            $scope.criteria.orderBy = {};
          }

          if ($scope.criteria.orderBy[name] === 'asc') {
            $scope.criteria.orderBy[name] = 'desc';
            return;
          }

          if ($scope.criteria.orderBy[name] === 'desc') {
            delete $scope.criteria.orderBy[name];
            return;
          }

          $scope.criteria.orderBy[name] = 'asc';
          $scope.criteria.page          = 1;
        };

        /**
         * @function toggleAll
         * @memberOf ListCtrl
         *
         * @description
         *   Toggles all items selection.
         */
        $scope.toggleAll = function() {
          if (!$scope.areAllSelected()) {
            $scope.selected.items = $scope.items.filter(function(item) {
              return $scope.isSelectable(item);
            }).map(function(item) {
              return $scope.getItemId(item);
            });
          } else {
            $scope.selected.items = [];
          }
        };

        /**
         * @function toggleColumns
         * @memberOf ListCtrl
         *
         * @description
         *   Toggles column filters container.
         */
        $scope.toggleColumns = function() {
          $scope.app.columns.collapsed = !$scope.app.columns.collapsed;
        };

        // Updates linkers when locale changes
        $scope.$watch('config.locale', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if (!$scope.config.multilanguage || !$scope.config.locale) {
            return;
          }

          for (var key in $scope.config.linkers) {
            $scope.config.linkers[key].setKey(nv);
            $scope.config.linkers[key].update();
          }
        });

        /**
         * @function toggleItem
         * @memberOf ListCtrl
         *
         * @description
         *   Selects/unselects an item when in grid mode.
         */
        $scope.toggleItem = function(item) {
          if ($scope.selected.items.indexOf($scope.getItemId(item)) < 0) {
            $scope.selected.items.push($scope.getItemId(item));
          } else {
            $scope.selected.items = $scope.selected.items.filter(function(el) {
              return el !== $scope.getItemId(item);
            });
          }
        };

        // Reloads the list when filters change.
        $scope.$watch('criteria', function(nv, ov) {
          if ($scope.tm) {
            $timeout.cancel($scope.tm);
          }

          if (nv === ov) {
            return;
          }

          // Reset page when criteria changes
          if (nv.page === ov.page) {
            nv.page = 1;
          }

          $scope.tm = $timeout(function() {
            $scope.list();
          }, 500);
        }, true);
      }
    ]);
})();
