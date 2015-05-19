(function () {
  'use strict';

  /**
   * Controller to handle list actions.
   */
  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  MarketListCtrl
     *
     * @requires $http
     * @requires $scope
     * @requires routing
     * @requires messenger
     * @requires webStorage
     *
     * @description
     *   Handles actions for market.
     */
    .controller('MarketListCtrl', [
      '$http', '$modal', '$scope', '$timeout', 'routing', 'messenger', 'webStorage',
      function($http, $modal, $scope, $timeout, routing, messenger, webStorage) {
        /**
         * @function addToCart
         * @memberOf MarketListCtrl
         *
         * @description
         *   Adds an item to the cart.
         *
         * @param {Object} item The item to add to cart.
         */
        $scope.addToCart = function(item) {
          if (!$scope.cart) {
            $scope.cart = [];
          }

          if ($scope.cart.indexOf(item) !== -1) {
            return;
          }

          $scope.cart.push(item);
        };

        /**
         * @function checkout
         * @memberOf MarketListctrl
         *
         * @description
         *   Opens a modal window to confirm the cart.
         *
         */
        $scope.checkout = function() {
          var modal = $modal.open({
            templateUrl: 'modal-checkout',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  cart: $scope.cart
                };
              },
              success: function() {
                return function() {
                  var url = routing.generate('backend_ws_market_checkout');
                  var data = $scope.cart.map(function(e) {
                    return e.id;
                  });

                  return $http.post(url, { modules: data });
                };
              }
            }
          });

          modal.result.then(function(response) {
            var message = response.data;
            var type    = response.status === 200 ? 'success' : 'error';

            messenger.post(message, type);

            if (response.status === 200) {
              $scope.cart = [];
            }
          });
        };

        /**
         * @function isActivated
         * @memberOf MarketListCtrl
         *
         * @description
         *   Checks if an item is already activated.
         *
         * @param {Object} name The item to check.
         *
         * @return {Boolean} True, if the item is already activated. Otherwise,
         *                   returns false.
         */
        $scope.isActivated = function(item) {
          return $scope.activated.indexOf(item.id) !== -1;
        };

        /**
         * @function isInCart
         * @memberOf MarketListCtrl
         *
         * @description
         *   Checks if an item is already in cart.
         *
         * @param {Object} item The item to check.
         *
         * @return {Boolean} True if the item is in the cart. Otherwise, returns
         *                   false.
         */
        $scope.isInCart = function(item) {
          if (!$scope.cart) {
            return false;
          }

          for (var i = 0; i < $scope.cart.length; i++) {
            if ($scope.cart[i].id === item.id) {
              return true;
            }
          }

          return false;
        };

        /**
         * @function list
         * @memberOf MarketListCtrl
         *
         * @description
         *   Finds the list of available modules.
         */
        $scope.list = function() {
          var url = routing.generate('backend_ws_market_list');

          $http.get(url).success(function(response) {
            $scope.activated = response.activated;
            $scope.items     = response.results;
          }).error(function(response) {
            messenger.post({ type: 'error', message: response });
          });
        };

        /**
         * @function removeFromCart
         * @memberOf MarketListCtrl
         *
         * @description
         *   Removes an item from cart.
         *
         * @param {Object} item  The item to remove.
         * @param {Object} event The click event object.
         */
        $scope.removeFromCart = function(item, event) {
          event.stopPropagation();

          $scope.cart.splice($scope.cart.indexOf(item), 1);
        };

        /**
         * @function showDetails
         * @memberOf MarketListCtrl
         *
         * @description
         *   Opens a modal window with the module details
         *
         * @param {Object} item The item to detail.
         */
        $scope.showDetails = function(item) {
          var modal = $modal.open({
            templateUrl: 'modal-details',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  activated: $scope.isActivated(item),
                  inCart:    $scope.isInCart(item),
                  item:      item
                };
              },
              success: function() {
                return null;
              }
            }
          });

          modal.result.then(function(response) {
            if (response) {
              $scope.addToCart(item);
            }
          });
        };

        // Save changes in chart in web storage
        $scope.$watch('cart', function(nv, ov) {
          if (!nv || (nv instanceof Array && nv.length === 0)) {
            $scope.empty = true;
            webStorage.local.remove('cart');
            return;
          }

          $scope.changing = true;
          webStorage.local.add('cart', nv);
          $timeout(function() {
            $scope.changing = false;
            $scope.empty    = false;
          }, 1000);
        }, true);

        // Initialize the shopping cart from the webStorage
        if (webStorage.local.has('cart')) {
          $scope.cart = webStorage.local.get('cart');
        }

        // Get modules list
        $scope.list();
    }]);
})();
