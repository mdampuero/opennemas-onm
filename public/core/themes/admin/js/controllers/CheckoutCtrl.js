(function() {
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
      '$rootScope', '$scope', '$window', 'http', 'webStorage',
      function($rootScope, $scope, $window, http, webStorage) {
        // List of territories excluded from VAT taxes
        var excluded = [
          'Ceuta', 'Melilla', 'Las Palmas', 'Santa Cruz de Tenerife'
        ];

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
        $scope.step = 0;

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
         * @function cancelCreditCard
         * @memberOf DomainCtrl
         *
         * @description
         *   Cancels credit card payment.
         */
        $scope.cancelCreditCard = function() {
          $scope.nonce    = null;
          $scope.payment  = null;
          $scope.total   -= $scope.fee;
          $scope.fee      = 0;
        };

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
        $scope.getPrice = function(item, type) {
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

          return http.put(route, data).then(function(response) {
            if (response.data.id) {
              $scope.purchase = response.data.id;
              webStorage.local.set('purchase', $scope.purchase);
            }

            $scope.step++;

            if ($scope.steps[$scope.step] === 'done') {
              webStorage.local.remove('purchase');
              webStorage.local.remove($scope.cartName + '_cart');
            }
          });
        };

        /**
         * @function getNotes
         * @memberOf CheckoutCtrl
         *
         * @description
         *   Returns the invoice notes.
         */
        $scope.getNotes = function() {
          var notes = '';

          for (var i = 0; i < $scope.cart.length; i++) {
            if ($scope.cart[i].notes) {
              notes += $scope.cart[i].notes.split('\n').join('<br>') + '<hr>';
            }
          }

          return notes.replace(/<hr>$/, '');
        };

        /**
         * @function previous
         * @memberOf CheckoutCtrl
         *
         * @description
         *   Goes to the previous step.
         */
        $scope.previous = function() {
          if ($scope.step > 0) {
            $scope.step--;
          }
        };

        /**
         * @function start
         * @memberOf CheckoutCtrl
         *
         * @description
         *   Starts the purchase.
         */
        $scope.start = function() {
          var route = {
            name: 'backend_ws_purchase_update',
            params: { id: $scope.purchase }
          };

          var data = $scope.getData();

          data.step = $scope.steps[0];

          return http.put(route, data)
            .then(function(response) {
              if (response.data.id) {
                $scope.purchase = response.data.id;
                webStorage.local.set('purchase', $scope.purchase);
              }
            });
        };

        /**
         * @function getTerms
         * @memberOf CheckoutCtrl
         *
         * @description
         *   Returns the invoice terms.
         */
        $scope.getTerms = function() {
          var terms = '';

          for (var i = 0; i < $scope.cart.length; i++) {
            if ($scope.cart[i].terms) {
              terms += $scope.cart[i].terms.split('\n').join('<br>') + '<hr>';
            }
          }

          return terms.replace(/<hr>$/, '');
        };

        // Get client after saving
        $rootScope.$on('client-saved', function(event, args) {
          $scope.client = args;
          $scope.next();
        });

        // Get subtotal on change
        $rootScope.$on('subtotal-changed', function(event, args) {
          $scope.subtotal = args;
        });

        // Updates tax when client changes
        $scope.$watch('client', function(nv) {
          if (!nv) {
            return;
          }

          if (nv.country === 'ES' && excluded.indexOf(nv.state) !== -1) {
            $scope.vatTax = 0;
            return;
          }

          if ($scope.taxes[nv.country] && (nv.country === 'ES' ||
              !nv.company && $scope.countries[nv.country])) {
            $scope.vatTax = $scope.taxes[nv.country].value;
          }
        }, true);

        // Update tax when vatTax or subtotal change
        $scope.$watch('[fee, subtotal, vatTax]', function() {
          $scope.tax = Number((($scope.subtotal + $scope.fee) * $scope.vatTax /
            100).toFixed(2));
        }, true);

        // Update total when fee, subtotal or tax change
        $scope.$watch('[fee, subtotal, tax]', function() {
          $scope.total = $scope.subtotal + $scope.tax + $scope.fee;
        }, true);

        // Update fee when payment changes
        $scope.$watch('payment', function(nv) {
          $scope.fee = 0;

          if (nv && $scope.subtotal > 0) {
            $scope.fee = Number(($scope.subtotal * 0.029 + 0.30).toFixed(2));
          }
        }, true);

        if (webStorage.local.has('purchase')) {
          $scope.purchase = webStorage.local.get('purchase');
        }

        // Update local storage when cart changes
        $scope.$watch('cart', function(nv) {
          webStorage.local.remove($scope.cartName);

          if (!nv || nv.length === 0) {
            return;
          }

          webStorage.local.set($scope.cartName, nv);
        }, true);

        // Configure braintree
        $scope.$watch('clientToken', function(nv) {
          if (!nv) {
            return;
          }

          if ($scope.clientToken && typeof braintree !== 'undefined') {
            $window.braintree.setup($scope.clientToken, 'dropin', {
              container: 'braintree-container',
              paypal: {
                container: 'braintree-container'
              },
              onError: function() {
                $scope.$apply(function() {
                  $scope.payment = null;
                  $scope.paymentLoading = false;
                });
              },
              onPaymentMethodReceived: function(obj) {
                $scope.$apply(function() {
                  $scope.payment        = obj;
                  $scope.paymentLoading = false;

                  $scope.next();
                });

                return false;
              }
            });

            $('#braintree-form').submit(function(e) {
              e.preventDefault();

              $scope.$apply(function() {
                $scope.paymentLoading = true;
              });

              return false;
            });
          }
        });
      }
    ]);
})();
