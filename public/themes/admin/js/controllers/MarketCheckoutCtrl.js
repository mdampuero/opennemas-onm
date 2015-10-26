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
            $scope.step = 3;
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

          $scope.iva = +($scope.subtotal * 0.21).toFixed(2);
          $scope.total = +($scope.subtotal + $scope.iva).toFixed(2);
        }, true);

        $scope.$watch('billing', function(nv, ov) {
          $scope.edit = false;

          if (!ov && !nv) {
            $scope.edit = true;
          }
        });

        // Initialize the shopping cart from the webStorage
        if (webStorage.local.has('cart')) {
          $scope.cart = webStorage.local.get('cart');
        }
      }
    ]);
})();
