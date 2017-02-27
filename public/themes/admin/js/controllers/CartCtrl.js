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
      '$rootScope', '$scope', 'webStorage',
      function ($rootScope, $scope, webStorage) {
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
        $scope.getPrice = function (item, type) {
          if (!type) {
            type = 'monthly';
          }

          if (!item.price ||
              item.price.length === 0) {
            return { value: 0 };
          }

          var prices = item.price.filter(function(a) {
            return a.type === type;
          });

          if (prices.length > 0) {
            return prices[0];
          }

          return item.price[0];
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
            subtotal +=
              $scope.getPrice($scope.cart[i], $scope.cart[i].priceType).value;
          }

          return subtotal;
        };

        // Force float type for prices
        $scope.$watch('cart', function(nv) {
          webStorage.local.remove($scope.cartName);

          if (!nv || nv.length === 0) {
            return;
          }

          webStorage.local.set($scope.cartName, nv);

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
