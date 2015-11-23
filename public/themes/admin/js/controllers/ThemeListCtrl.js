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
    .controller('ThemeListCtrl', [
      '$analytics', '$http', '$modal', '$scope', '$timeout', 'routing', 'messenger', 'webStorage',
      function($analytics, $http, $modal, $scope, $timeout, routing, messenger, webStorage) {
        /**
         * The available modules.
         *
         * @type {Array}
         */
        $scope.available = [];

        /**
         * The type of the modules to list.
         *
         * @type {String}
         */
        $scope.type = 'available';

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
         * @function allActivated
         * @memberOf MarketListCtrl
         *
         * @description
         *   Check if all modules from array are already activated.
         *
         * @param {Array} source The array of modules to check.
         *
         * @return {Boolean} True if all modules are already activated.
         *                   Otherwise, returns false.
         */
        $scope.allActivated = function(source) {
          if (!source) {
            return true;
          }

          for (var i = 0; i < source.length; i++) {
            if (source[i].type !== 'internal' &&
                $scope.activated.indexOf(source[i].id) === -1) {
              return false;
            }
          }

          return true;
        };

        /**
         * @function allDeactivated
         * @memberOf MarketListCtrl
         *
         * @description
         *   Check if all modules from array are deactivated.
         *
         * @param {Array} source The array of modules to check.
         *
         * @return {Boolean} True if all modules are deactivated. Otherwise,
         *                   returns false.
         */
        $scope.allDeactivated = function(source) {
          if (!source) {
            return true;
          }

          for (var i = 0; i < source.length; i++) {
            if (source[i].type !== 'internal' &&
                $scope.activated.indexOf(source[i].id) !== -1) {
              return false;
            }
          }

          return true;
        };

        /**
         * @function list
         * @memberOf MarketListCtrl
         *
         * @description
         *   Finds the list of available modules.
         */
        $scope.enable = function(item) {
          item.loading = true;

          var url = routing.generate('backend_ws_theme_enable',
              { uuid: item.uuid });

          $http.get(url).success(function() {
            $scope.active = item.uuid;
            item.loading = false;
          }).error(function() {
            item.loading = false;
          });
        };

        /**
         * @function isActive
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
        $scope.isActive = function(item) {
          return $scope.active === item.uuid;
        };

        /**
         * @function isEnabled
         * @memberOf MarketListCtrl
         *
         * @description
         *   Checks if an item is already enabled.
         *
         * @param {Object} name The item to check.
         *
         * @return {Boolean} True, if the item is already enabled. Otherwise,
         *                   returns false.
         */
        $scope.isPurchased = function(item) {
          return $scope.purchased.indexOf(item) !== -1;
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
            if ($scope.cart[i].uuid === item.uuid) {
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
          $scope.loading = true;
          var url = routing.generate('backend_ws_theme_list');

          $http.get(url).success(function(response) {
            $scope.active          = response.active;
            $scope.available       = response.themes;
            $scope.exclusive       = response.exclusive;

            $scope.purchased = [];
            for (var i = 0; i < response.themes.length; i++) {
              if (response.purchased.indexOf(response.themes[i].uuid) !== -1) {
                $scope.purchased.push(response.themes[i]);
              }
            }

            $scope.items = $scope[$scope.type];

            $scope.loading = false;
          }).error(function(response) {
            $scope.loading = false;
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
            windowClass: 'modal-details-theme',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  isActive:    $scope.isActive,
                  enable:      $scope.enable,
                  inCart:      $scope.isInCart(item),
                  item:        item,
                  lang:        $scope.lang,
                  isPurchased: $scope.isPurchased,
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
            webStorage.local.remove('themes-cart');
            return;
          }

          webStorage.local.add('themes-cart', nv);

          // Adding first item or initialization from webstorage
          if (!ov || (ov instanceof Array && ov.length === 0) || ov === nv) {
            $scope.bounce = true;
            $timeout(function() { $scope.bounce = false; }, 1000);
            return;
          }

          // Adding items
          $scope.pulse = true;
          $timeout(function() { $scope.pulse = false; }, 1000);
        }, true);

        // Change current items when type changes
        $scope.$watch('type', function(nv) {
          if (!nv) {
            return;
          }

          $scope.items = $scope[nv];
        });

        // Initialize the shopping cart from the webStorage
        if (webStorage.local.has('themes-cart')) {
          $scope.cart = webStorage.local.get('themes-cart');
        }

        // Get modules list
        $scope.list();
    }]);
})();
