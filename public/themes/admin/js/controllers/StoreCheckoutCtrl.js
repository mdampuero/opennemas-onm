(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  StoreCheckoutCtrl
     *
     * @requires $analytics
     * @requires $http
     * @requires $scope
     * @requires messenger
     * @requires routing
     * @requires webStorage
     *
     * @description
     *   Controller to handle actions in checkout.
     */
    .controller('StoreCheckoutCtrl', [
      '$analytics', '$controller', '$rootScope', '$scope', 'http', 'messenger', 'routing', 'webStorage',
      function ($analytics, $controller, $rootScope, $scope, http, messenger, routing, webStorage) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('CheckoutCtrl',
            { $rootScope: $rootScope, $scope: $scope }));

        /**
         * @memberOf StoreCheckoutCtrl
         *
         * @description
         *  The shopping cart name.
         *
         * @type {String}
         */
        $scope.cartName = 'cart_store';

        /**
         * @memberOf StoreCheckoutCtrl
         *
         * @description
         *   The name for steps.
         *
         * @type {Array}
         */
        $scope.steps = [ 'cart', 'billing', 'payment', 'summary', 'done' ];

        /**
         * @function confirm
         * @memberOf StoreCheckoutCtrl
         *
         * @description
         *  Requests the purchase and shows a confirmation modal window.
         */
        $scope.confirm = function() {
          $scope.loading = true;

          var data = { purchase: $scope.purchase };

          if ($scope.payment.type) {
            data.method = $scope.payment.type;
            data.nonce  = $scope.payment.nonce;
          }

          http.post('backend_ws_store_checkout', data).then(function() {
            $scope.next().then(function() {
              $scope.cart = [];
              webStorage.local.remove($scope.cartName);
              webStorage.local.remove('purchase');
              $analytics.pageTrack('/store/checkout/done');
            });
          }, function() {
            $scope.error   = true;
            $scope.loading = false;
          });
        };

        /**
         * @function getData
         * @memberOf StoreCheckoutCtrl
         *
         * @description
         *   Returns the data to send basing on the current purchase status.
         *
         * @return {Object} The data to send.
         */
        $scope.getData = function() {
          var ids = {};
          for (var i = 0; i < $scope.cart.length; i++) {
            ids[$scope.cart[i].uuid] = $scope.cart[i].priceType ?
              $scope.cart[i].priceType : 'monthly';
          }

          var data = {
            ids:    ids,
            step:   $scope.steps[$scope.step + 1],
          };

          if ($scope.payment.type) {
            data.method = $scope.payment.type;
          }

          return data;
        };

        // Initialize the shopping cart from the webStorage
        if (webStorage.local.has($scope.cartName)) {
          $scope.cart = webStorage.local.get($scope.cartName);
        }

        if (!$scope.purchase) {
          var data = $scope.getData();

          http.post('backend_ws_purchase_save', data).then(function(response) {
            $scope.purchase = response.data.id;
            webStorage.local.set('purchase', $scope.purchase);
          });
        } else {
          $scope.start();
        }
      }
    ]);
})();
