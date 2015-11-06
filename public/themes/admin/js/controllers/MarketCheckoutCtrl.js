(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  MarketCheckoutCtrl
     *
     * @requires $http
     * @requires $scope
     * @requires routing
     *
     * @description
     *   description
     */
    .controller('MarketCheckoutCtrl', ['$analytics', '$http', '$modal', '$scope', 'messenger', 'routing', 'webStorage',
      function ($analytics, $http, $modal, $scope, messenger, routing, webStorage) {
        /**
         * @memeberOf MarketCheckoutCtrl
         *
         * @description
         *   Flag to edit billing information.
         *
         * @type {Boolean}
         */
        $scope.edit = false;

        /**
         * @memeberOf MarketCheckoutCtrl
         *
         * @description
         *   The current step in the checkout wizard.
         *
         * @type {Boolean}
         */
        $scope.step = 1;

        /**
         * @memeberOf MarketCheckoutCtrl
         *
         * @description
         *   Flag to know if current VAT is valid.
         *
         * @type {Boolean}
         */
        $scope.validVat = false;

        /**
         * @function confirm
         * @memberOf MarketCheckoutCtrl
         *
         * @description
         *  Requests the purchase and shows a confirmation modal window.
         */
        $scope.confirm = function() {
          $scope.saving = true;
          var url = routing.generate('backend_ws_market_checkout');
          var modules = $scope.cart.map(function(e) {
            return e.id;
          });

          var data = { billing: $scope.billing, modules: modules };

          $http.post(url, data).success(function(response) {
            $scope.step = 4;
            $scope.cart = [];
            webStorage.local.remove('cart');
            $analytics.pageTrack('/market/checkout/done');
          }).error(function() {
            messenger.post({
              message: 'There was an error with your request',
              type: 'error'
            });
          });
        };

        /**
         * @function setStep
         * @memberOf MarketCheckoutCtrl
         *
         * @description
         *  Sets the step.
         */
        $scope.setStep = function(step) {
          $scope.step = step;
        };

        /**
         * @function removeFromCart
         * @memberOf MarketCheckoutCtrl
         *
         * @description
         *   Removes an item from cart.
         *
         * @param {Object} item  The item to remove.
         */
        $scope.removeFromCart = function(item) {
          $scope.cart.splice($scope.cart.indexOf(item), 1);
        };

        // Updates the edit flag when billing changes.
        $scope.$watch('billing', function(nv, ov) {
          if (!ov.name && !nv.name) {
            $scope.edit = true;
          }
        });

        // Updates the total when the cart changes
        $scope.$watch('cart', function(nv) {
          $scope.subtotal = 0;
          $scope.total = 0;

          if (!nv || (nv instanceof Array && nv.length === 0)) {
            webStorage.local.remove('cart');
            return;
          }

          webStorage.local.add('cart', nv);

          for (var i = 0; i < nv.length; i++) {
            if (nv[i].price && nv[i].price.month) {
              $scope.subtotal += nv[i].price.month;
            }
          }
        }, true);

        // Updates vat and total values when vat tax changes
        $scope.$watch('validVat', function(nv, ov) {
          if (nv === true) {
            $scope.vat   = ($scope.subtotal * $scope.vatTax) / 100;
            $scope.total = $scope.subtotal + $scope.vat;
          }
        });

        // Updates the edit flag when billing changes.
        $scope.$watch('[billing.country, billing.vat]', function() {
          if (!$scope.billing.country || !$scope.billing.vat) {
            return;
          }

          var url = routing.generate('backend_ws_market_check_vat',
              { 'country': $scope.billing.country, 'vat': $scope.billing.vat });

          $http.get(url).then(function(response) {
            if (response.status === 200) {
              $scope.vatTax = parseFloat(response.data);
              $scope.validVat = true;
            }
          });
        }, true);

        // Initialize the shopping cart from the webStorage
        if (webStorage.local.has('cart')) {
          $scope.cart = webStorage.local.get('cart');
        }
      }
    ]);
})();
