(function () {
  'use strict';

  /**
   * Controller to handle list actions.
   */
  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  ThemeListCtrl
     *
     * @requires $http
     * @requires $scope
     * @requires routing
     * @requires messenger
     * @requires webStorage
     *
     * @description
     *   Handles actions for themes store.
     */
    .controller('ThemeListCtrl', [
      '$analytics', '$http', '$location', '$uibModal', '$scope', '$timeout', 'routing', 'messenger', 'webStorage',
      function($analytics, $http, $location, $uibModal, $scope, $timeout, routing, messenger, webStorage) {
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
         * @memberOf ThemeListCtrl
         *
         * @description
         *   Adds an item to the cart.
         *
         * @param {Object} item The item to add to cart.
         */
        $scope.addToCart = function(item) {
          $timeout(function() {
            if (!$scope.cart) {
              $scope.cart = [];
            }

            if ($scope.cart.indexOf(item) !== -1) {
              return;
            }

            $scope.cart.push(item);
          }, 1500);
        };

        /**
         * @function toggleCustom
         * @memberOf ThemeListCtrl
         *
         * @description
         *   Customize the theme.
         */
        $scope.toggleCustom = function(item) {
          if (!item.customize) {
            item.name = item.name.replace('(Custom)', '');
            item.price.single = 350;
            item.price.month  = 35;
          } else {
            item.name = item.name + ' (Custom)';
            item.price.single = 1450;
            item.price.month  = 135;
          }
        };

        /**
         * @function list
         * @memberOf ThemeListCtrl
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
         * @memberOf ThemeListCtrl
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
         * @memberOf ThemeListCtrl
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
         * @memberOf ThemeListCtrl
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
         * @memberOf ThemeListCtrl
         *
         * @description
         *   Finds the list of available modules.
         */
        $scope.list = function() {
          $scope.loading = true;
          var url = routing.generate('backend_ws_theme_list');

          $http.get(url).success(function(response) {
            $scope.active     = response.active;
            $scope.exclusive  = response.exclusive;

            $scope.purchased = [];
            for (var i = 0; i < response.themes.length; i++) {
              if (response.purchased.indexOf(response.themes[i].uuid) !== -1) {
                $scope.purchased.push(response.themes[i]);
              }
            }

            $scope.available = [];
            for (var i = 0; i < response.themes.length; i++) {
              if (!$scope.isPurchased(response.themes[i]) &&
                  !response.themes[i].exclusive) {
                $scope.available.push(response.themes[i]);
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
         * @memberOf ThemeListCtrl
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
         * @memberOf ThemeListCtrl
         *
         * @description
         *   Opens a modal window with the module details
         *
         * @param {Object} item The item to detail.
         */
        $scope.showDetails = function(item) {
          var modal = $uibModal.open({
            templateUrl: 'modal-details',
            windowClass: 'modal-details-theme',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  addToCart:    $scope.addToCart,
                  enable:       $scope.enable,
                  isActive:     $scope.isActive,
                  isInCart:     $scope.isInCart,
                  isPurchased:  $scope.isPurchased,
                  item:         item,
                  lang:         $scope.lang,
                  toggleCustom: $scope.toggleCustom,
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
            webStorage.local.remove('cart');
            return;
          }

          webStorage.local.set('cart', nv);

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

          $location.path(nv);
          $scope.items = $scope[nv];
        });

        // Initialize the shopping cart from the webStorage
        if (webStorage.local.has('cart')) {
          $scope.cart = webStorage.local.get('cart');
        }

        // Initialize the type from current location
        if ($location.path()) {
          $scope.type = $location.path().replace('/', '');
        }

        // Get modules list
        $scope.list();
    }]);
})();
