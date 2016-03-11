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
      '$scope', '$timeout',
      function($scope, $timeout) {
        /**
         * @memberOf ListCtrl
         *
         * @description
         *   The current pagination status.
         *
         * @type {Object}
         */
        $scope.pagination = { epp: 25, page: 1, total: 0 };

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
          var i = 0;
          while (i < $scope.orderBy.length && $scope.orderBy[i].name !== name) {
            i++;
          }

          if (i < $scope.orderBy.length) {
            return $scope.orderBy[i].value;
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
          var i = 0;
          while (i < $scope.orderBy.length && $scope.orderBy[i].name !== name) {
            i++;
          }

          if (i >= $scope.orderBy.length) {
            $scope.orderBy.push({
              name: name,
              value: 'asc'
            });
          } else {
            if ($scope.orderBy[i].value === 'asc') {
              $scope.orderBy[i].value = 'desc';
            } else {
              $scope.orderBy.splice(i, 1);
            }
          }

          $scope.pagination.page = 1;
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

        // Reloads the list when filters change.
        $scope.$watch('criteria', function(nv, ov) {
          if ($scope.searchTimeout) {
            $timeout.cancel($scope.searchTimeout);
          }

          if (nv === ov) {
            return;
          }

          if ($scope.pagination.page !== 1) {
            $scope.pagination.page = 1;
            return;
          }

          $scope.searchTimeout = $timeout(function() {
            $scope.list();
          }, 500);
        }, true);

        // Refresh the list of elements when some parameter changes
        $scope.$watch('[orderBy, pagination.epp, pagination.page]', function(nv, ov) {
          if ($scope.searchTimeout) {
            $timeout.cancel($scope.searchTimeout);
          }

          if (nv === ov) {
            return;
          }

          $scope.searchTimeout = $timeout(function() {
            $scope.list();
          }, 500);
        }, true);
      }
    ]);
})();
