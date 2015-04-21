(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  MenuCtrl
     *
     * @requires $controller
     * @requires $http
     * @requires $modal
     * @requires $rootScope
     * @requires $scope
     * @requires routing
     *
     * @description
     *   Handle actions for article inner.
     */
    .controller('MenuCtrl', ['$controller', '$http', '$modal', '$rootScope', '$scope', 'routing',
      function($controller, $http, $modal, $rootScope, $scope, routing) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @function open
         * @memberOf MenuCtrl
         *
         * @description
         *   Opens a modal window to add item to menu.
         */
        $scope.open = function() {
          var modal = $modal.open({
            templateUrl: 'modal-add-item',
            backdrop: 'static',
            controller: 'MenuModalCtrl'
          });

          modal.result.then(function(response) {
            $scope.menu.items = $scope.menu.items.concat(response.items);
          });
        };

        /**
         * @function removeItem
         * @memberOf MenuCtrl
         *
         * @description
         *   Deletes an item from the menu.
         *
         * @param {Integer} index The index of the item to remove.
         */
        $scope.removeItem = function(index, parentIndex) {
          if (angular.isUndefined(parentIndex)) {
            $scope.menu.items.splice(index, 1);
            return;
          }

          $scope.menu.items[parentIndex].submenu.splice(index, 1);
        };

        // Updates the menu items input value when menu items change.
        $scope.$watch('menu.items', function() {
          $scope.menuItems = angular.toJson($scope.menu.items);
        }, true)

        // Prevent form submit on enter key press
        $('.menu-items').on('keypress', function(e) {
          if (e.keyCode === 13) {
            e.preventDefault();
          }
        });
      }
    ]);
})();

