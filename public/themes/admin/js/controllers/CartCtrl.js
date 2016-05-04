(function() {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  CartCtrl
     *
     * @requires $rootScope
     * @requires $scope
     *
     * @description
     *   Manages actions for cart.
     */
    .controller('CartCtrl', [
      '$rootScope', '$scope',
      function ($rootScope, $scope) {
        /**
         * @function getPrice
         * @memberOf CartCtrl
         *
         * @description
         *   Returns the price for an item.
         *
         * @param {Integer} index The position of the item.
         * @param {String}  type  The type of the price.
         *
         * @return {Float} The item price.
         */
        $scope.getPrice = function (index, type) {
          if (!type) {
            type = 'monthly';
          }

          if (!$scope.cart[index].price ||
              $scope.cart[index].price.length === 0) {
            return { value: 0 };
          }

          var prices = $scope.cart[index].price.filter(function(a) {
            return a.type === type;
          });

          if (prices.length > 0) {
            return prices[0];
          }

          return $scope.cart[index].price[0];
        };

        /**
         * @function getSubTotal
         * @memberOf CartCtrl
         *
         * @description
         *   Returns the total price of the items in cart.
         *
         * @return {Float} The total price of the items in cart.
         */
        $scope.getSubTotal = function () {
          if (!$scope.cart) {
            return 0;
          }

          var subtotal = 0;

          for (var i = 0; i < $scope.cart.length; i++) {
            subtotal += $scope.getPrice(i).value;
          }

          return subtotal;
        };

        // Force float type for prices
        $scope.$watch('cart', function(nv) {
          if (!nv || nv.length === 0) {
            return;
          }

          for (var i = 0; i < nv.length; i++) {
            for (var j = 0; j < nv[i].price.length; j++) {
              nv[i].price[j].value = parseFloat(nv[i].price[j].value);
            }
          }

          $rootScope.$broadcast('subtotal-changed', $scope.getSubTotal());
        }, true);
      }
    ]);
})();
