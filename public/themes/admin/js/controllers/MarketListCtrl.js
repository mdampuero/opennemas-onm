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
      '$http', '$scope', 'routing', 'messenger',
      function($http, $scope, routing, messenger) {
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
          return $scope.cart && $scope.cart.indexOf(item) !== -1;
        };

        $scope.list = function() {
          var url = routing.generate('backend_ws_market_list');

          $http.get(url).success(function(response) {
            $scope.contents = response.results;
          }).error(function() {
            messenger.post({ type: 'error' });
          });
        };

        $scope.list();
    }]);
})();
