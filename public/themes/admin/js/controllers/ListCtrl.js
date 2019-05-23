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
         * The available elements per page
         *
         * @type {Array}
         */
        $scope.views = [ 10, 25, 50, 100 ];

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

          $scope.tm = $timeout(function() {
            $scope.app.columns.collapsed = true;
          }, 500);
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

          $scope.tm = $timeout(function() {
            $scope.app.columns.collapsed = false;
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
          return $scope.app.columns.selected.indexOf(name) !== -1;
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
            if ($scope.criteria.page !== 1) {
              $scope.criteria.page = 1;
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
          $scope.app.columns.collapsed = !$scope.app.columns.collapsed;

          if (!$scope.app.columns.collapsed) {
            $scope.scrollTop();
          }
        };

        // Marks variables to delete for garbage collector
        $scope.$on('$destroy', function() {
          $scope.criteria = null;
          $scope.config   = null;
          $scope.items    = null;
          $scope.selected = null;
        });

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
