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
         *   The name for steps.
         *
         * @type {Array}
         */
        $scope.steps = [ 'cart', 'billing', 'summary', 'done' ];

        /**
         * @function confirm
         * @memberOf StoreCheckoutCtrl
         *
         * @description
         *  Requests the purchase and shows a confirmation modal window.
         */
        $scope.confirm = function() {
          $scope.saving = true;
          var modules = $scope.cart.map(function(e) {
            var id = e.uuid;
            if (!id) {
              id = e.id;
            }
            return id;
          });

          var data = { client: $scope.client, modules: modules };

          http.post('backend_ws_store_checkout', data).then(function() {
            $scope.next().then(function() {
              $scope.cart = [];
              webStorage.local.remove('cart');
              webStorage.local.remove('purchase');
              $analytics.pageTrack('/store/checkout/done');
            });
          }, function(response) {
            $scope.loading = false;
            messenger.post({ message: response.data, type: 'error' });
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
            ids[$scope.cart[i].uuid] = $scope.cart[i].customize ? 1 : 0;
          }

          return {
            ids:   ids,
            step:  $scope.steps[$scope.step],
          };
        };

        // Initialize the shopping cart from the webStorage
        if (webStorage.local.has('cart')) {
          $scope.cart = webStorage.local.get('cart');
        }

        if (webStorage.local.has('purchase')) {
          $scope.purchase = webStorage.local.get('purchase');
        }

        if (!$scope.purchase) {
          http.post('backend_ws_purchase_save').then(function(response) {
            $scope.purchase = response.data;
            webStorage.local.set('purchase', $scope.purchase);
          });
        }
      }
    ]);
})();
