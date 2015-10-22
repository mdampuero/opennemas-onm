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
    .controller('MarketCheckoutCtrl', ['$analytics', '$http', '$modal', '$scope', 'routing', 'webStorage',
      function ($analytics, $http, $modal, $scope, routing, webStorage) {
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
            var modal = $modal.open({
              keyboard: false,
              templateUrl: 'modal-checkout',
              backdrop: 'static',
              controller: 'modalCtrl',
              resolve: {
                template: function() {
                  return null;
                },
                success: function() {
                  return null;
                }
              }
            });

            modal.result.then(function(response) {
              webStorage.local.remove('cart');
              $analytics.pageTrack('/market/checkout/done');
              window.location.href = routing.generate('admin_market_list');
            });
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
          $scope.total = 0;

          if (!nv || (nv instanceof Array && nv.length === 0)) {
            webStorage.local.remove('cart');
            return;
          }

          webStorage.local.add('cart', nv);

          for (var i = 0; i < nv.length; i++) {
            if (nv[i].price && nv[i].price.month) {
              $scope.total += nv[i].price.month;
            }
          }
        }, true);

        // Initialize the shopping cart from the webStorage
        if (webStorage.local.has('cart')) {
          $scope.cart = webStorage.local.get('cart');
        }
      }
    ]);
})();
