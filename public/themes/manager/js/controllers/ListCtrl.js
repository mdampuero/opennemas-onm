(function () {
  'use strict';

  angular.module('ManagerApp')
    /**
     * @ngdoc controller
     * @name  ListCtrl
     *
     * @requires $scope
     * @requires $timeout
     *
     * @description
     *   Generic controller for lists.
     */
    .controller('ListCtrl', [
      '$location', '$scope', '$timeout', 'oqlDecoder',
      function($location, $scope, $timeout, oqlDecoder) {
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
         * @function cleanCriteria
         * @memberOf ListCtrl
         *
         * @description
         *   Removes empty values from criteria.
         *
         * @param {Object} The criteria object.
         *
         * @return {Object} The cleaned criteria object.
         */
        $scope.cleanCriteria = function(criteria) {
          var cleaned = {};

          for (var name in criteria) {
            if (criteria[name] !== null &&
                criteria[name] !== undefined &&
                criteria[name] !== '') {
              cleaned[name] = criteria[name];
            }
          }

          return cleaned;
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
         * @function closeColumns
         * @memberOf ClientListCtrl
         *
         * @description
         *   Hides the dropdown to toggle table columns.
         */
        $scope.closeColumns = function() {
          if ($scope.tm) {
            $timeout.cancel($scope.tm);
          }

          $scope.tm = $timeout(function () {
            $scope.open = false;
          }, 500);
        };

        /**
         * @function openColumns
         * @memberOf ClientListCtrl
         *
         * @description
         *   Shows the dropdown to toggle table columns.
         */
        $scope.openColumns = function() {
          if ($scope.tm) {
            $timeout.cancel($scope.tm);
          }

          $scope.tm = $timeout(function () {
            $scope.open = true;
          }, 500);
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
          return $scope.columns.selected.indexOf(name) !== -1;
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
            if ($scope.pagination.page !== 1) {
              $scope.pagination.page = 1;
              return;
            }

            $scope.list();
          }
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
          if ($scope.selected.all) {
            $scope.selected.items = $scope.items.map(function(item) {
              return item.id;
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
          $scope.columns.collapsed = !$scope.columns.collapsed;

          if (!$scope.columns.collapsed) {
            $scope.scrollTop();
          }
        };

        // Marks variables to delete for garbage collector
        $scope.$on('$destroy', function() {
          $scope.criteria   = null;
          $scope.columns    = null;
          $scope.pagination = null;
          $scope.items      = null;
          $scope.selected   = null;
          $scope.orderBy    = null;
        });

        // Update criteria when route changes
        $scope.$on('$routeUpdate', function() {
          if (!$location.search().oql) {
            return;
          }

          var criteria = oqlDecoder.decode($location.search().oql);

          if (!angular.equals(criteria, $scope.criteria)) {
            $scope.criteria = criteria;
          }
        });

        // Reloads the list when filters change.
        $scope.$watch('criteria', function(nv, ov) {
          if ($scope.searchTimeout) {
            $timeout.cancel($scope.searchTimeout);
          }

          if (nv === ov) {
            return;
          }

          if(nv.page && ov.page && nv.page === ov.page) {
            nv.page = 1;
          }

          // Remove empty values from criteria
          $scope.criteria = $scope.cleanCriteria(nv);

          $scope.searchTimeout = $timeout(function() {
            $scope.list();
          }, 500);
        }, true);
      }
    ]);
})();
