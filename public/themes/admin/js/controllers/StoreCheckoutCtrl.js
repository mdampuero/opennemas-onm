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
      '$analytics', '$controller', '$http', '$rootScope', '$scope', 'messenger', 'routing', 'webStorage',
      function ($analytics, $controller, $http, $rootScope, $scope, messenger, routing, webStorage) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('CheckoutCtrl',
            { $rootScope: $rootScope, $scope: $scope }));

        /**
         * @function confirm
         * @memberOf StoreCheckoutCtrl
         *
         * @description
         *  Requests the purchase and shows a confirmation modal window.
         */
        $scope.confirm = function() {
          $scope.saving = true;
          var url = routing.generate('backend_ws_store_checkout');
          var modules = $scope.cart.map(function(e) {
            var id = e.uuid;
            if (!id) {
              id = e.id;
            }
            return id;
          });

          var data = { client: $scope.client, modules: modules };

          $http.post(url, data).then(function() {
            $scope.next();
            $scope.cart = [];
            webStorage.local.remove('cart');
            $analytics.pageTrack('/store/checkout/done');
          }, function(response) {
            $scope.loading = false;
            messenger.post({ message: response.data, type: 'error' });
          });
        };

        // Initialize the shopping cart from the webStorage
        if (webStorage.local.has('cart')) {
          $scope.cart = webStorage.local.get('cart');
        }
      }
    ]);
})();
