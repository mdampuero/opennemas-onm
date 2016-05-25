(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  CheckoutCtrl
     *
     * @requires $rootScope
     * @requires $scope
     *
     * @description
     *   Controller to handle checkout-related actions.
     */
    .controller('CheckoutCtrl', [
      '$rootScope', '$scope', 'http', 'webStorage',
      function($rootScope, $scope, http, webStorage) {
        /**
         * @memberOf CheckoutCtrl
         *
         * @description
         *   Array of cart.
         *
         * @type {Array}
         */
        $scope.cart = [];

        /**
         * @memberOf CheckoutCtrl
         *
         * @description
         *   The fee to add basing on the purchase and the payment.
         *
         * @type {Float}
         */
        $scope.fee = 0;

        /**
         * @memberOf CheckoutCtrl
         *
         * @description
         *  The current payment method.
         *
         * @type {String}
         */
        $scope.payment = {};

        /**
         * @memberOf CheckoutCtrl
         *
         * @description
         *   The current step in the checkout wizard.
         *
         * @type {Boolean}
         */
        $scope.step = 1;

        /**
         * @memberOf CheckoutCtrl
         *
         * @description
         *   The price of the items to purchase without taxes.
         *
         * @type {Float}
         */
        $scope.subtotal = 0;

        /**
         * @memberOf CheckoutCtrl
         *
         * @description
         *   The amount to add to the purchase after apply the VAT tax.
         *
         * @type {Float}
         */
        $scope.tax = 0;

        /**
         * @memberOf CheckoutCtrl
         *
         * @description
         *   The total price of the purchase.
         *
         * @type {Float}
         */
        $scope.total = 0;

        /**
         * @memberOf CheckoutCtrl
         *
         * @description
         *   The VAT tax to apply.
         *
         * @type {Boolean}
         */
        $scope.vatTax = 0;

        /**
         * @function getPrice
         * @memberOf CheckoutCtrl
         *
         * @description
         *   Returns the item price.
         *
         * @param {Object} item  The item.
         * @param {String} price The price type.
         *
         * @return {Float} The item price.
         */
        $scope.getPrice = function (item, type) {
          if (!type) {
            type = 'monthly';
          }

          if (!item.price || item.price.length === 0) {
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
         * @function next
         * @memberOf CheckoutCtrl
         *
         * @description
         *   Goes to the next step.
         */
        $scope.next = function() {
          var route = {
            name: 'backend_ws_purchase_update',
            params: { id: $scope.purchase }
          };

          var data = $scope.getData();

          return http.put(route, data).then(function() {
            $scope.step++;

            if ($scope.steps[$scope.step] === 'done') {
              webStorage.local.remove('purchase');
            }
          });
        };

        /**
         * @function previous
         * @memberOf CheckoutCtrl
         *
         * @description
         *   Goes to the previous step.
         */
        $scope.previous = function() {
          if ($scope.step > 1) {
            $scope.step--;
          }
        };

        // Get client after saving
        $rootScope.$on('client-saved', function (event, args) {
          $scope.client = args;
          $scope.next();
        });

        // Get subtotal on change
        $rootScope.$on('subtotal-changed', function (event, args) {
          $scope.subtotal = args;
        });

        // Updates tax when client changes
        $scope.$watch('client', function(nv) {
          if (!nv) {
            return;
          }

          if ($scope.taxes[nv.country] && (nv.country === 'ES' ||
              (!nv.company && $scope.countries[nv.country]))) {
            $scope.vatTax = $scope.taxes[nv.country].value;
          }
        }, true);

        // Update tax when vatTax or subtotal change
        $scope.$watch('[vatTax, subtotal]', function() {
          $scope.tax = Math.round($scope.subtotal * $scope.vatTax)/100;
        }, true);

        // Update total when fee, subtotal or tax change
        $scope.$watch('[fee, subtotal, tax]', function () {
          $scope.total = $scope.subtotal + $scope.tax + $scope.fee;
        }, true);
      }
    ]);
})();
