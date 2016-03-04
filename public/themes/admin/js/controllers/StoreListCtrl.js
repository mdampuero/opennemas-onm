(function () {
  'use strict';

  /**
   * Controller to handle list actions.
   */
  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  StoreListCtrl
     *
     * @requires $http
     * @requires $scope
     * @requires routing
     * @requires messenger
     * @requires webStorage
     *
     * @description
     *   Handles actions for store.
     */
    .controller('StoreListCtrl', [
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
        $scope.type = 'pack';

        /**
         * @function addToCart
         * @memberOf StoreListCtrl
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
         * @memberOf StoreListCtrl
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
                $scope.activated.indexOf(source[i].uuid) === -1) {
              return false;
            }
          }

          return true;
        };

        /**
         * @function allDeactivated
         * @memberOf StoreListCtrl
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
                $scope.activated.indexOf(source[i].uuid) !== -1) {
              return false;
            }
          }

          return true;
        };

        /**
         * @function isActivated
         * @memberOf StoreListCtrl
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
          if ($scope.activated.indexOf(item.uuid) !== -1) {
            return true;
          }

          if (item.metas && item.metas.modules_included) {
            var notActivated = item.metas.modules_included.filter(function(a) {
              return $scope.activated.indexOf(a) === -1;
            });

            return notActivated.length === 0;
          }

          return false;
        };

        /**
         * @function isInCart
         * @memberOf StoreListCtrl
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

          var itemsInCart = [];
          for (var i = 0; i < $scope.cart.length; i++) {
            itemsInCart.push($scope.cart[i].uuid);

            if ($scope.cart[i].metas && $scope.cart[i].metas.modules_included) {
              itemsInCart = itemsInCart.concat(
                  $scope.cart[i].metas.modules_included);
            }
          }

          // Item in cart
          if (itemsInCart.indexOf(item.uuid) !== -1) {
            return true;
          }

          return false;
        };

        /**
         * @function isFree
         * @memberOf StoreListCtrl
         *
         * @description
         *   Checks if a module is free.
         *
         * @param {Object} module The module to check
         *
         * @return {Boolean} True if the module is free. Otherwise, returns
         *                   false.
         */
        $scope.isFree = function(module) {
          if (!module.metas || !module.metas.price) {
            return true;
          }

          for (var i = 0; i < module.metas.price.length; i++) {
            if (module.metas.price[i].value == 0) {
              return true;
            }
          }

          return false;
        };

        /**
         * @function list
         * @memberOf StoreListCtrl
         *
         * @description
         *   Finds the list of available modules.
         */
        $scope.list = function() {
          $scope.loading = true;
          var url = routing.generate('backend_ws_store_list');

          $http.get(url).success(function(response) {
            $scope.activated = response.activated;

            $scope.free      = [];
            $scope.module    = [];
            $scope.pack      = [];
            $scope.partner   = [];
            $scope.service   = [];
            $scope.purchased = [];
            for (var i = 0; i < response.results.length; i++) {
              var module = response.results[i];

              if ($scope.isFree(module) &&
                  module.metas.category !== 'partner') {
                $scope.free.push(module);
              } else if (response.activated.indexOf(module.uuid) !== -1) {
                $scope.purchased.push(module);
              } else {
                if (module.metas && module.metas.modules_included) {
                  // Check submodules
                  var activated = module.metas.modules_included.filter(function(a) {
                    return response.activated.indexOf(a) === -1;
                  });

                  if (activated.length === 0) {
                    $scope.purchased.push(module);
                  } else {
                    $scope[module.metas.category].push(module);
                  }
                }  else {
                  $scope[module.metas.category].push(module);
                }
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
         * @memberOf StoreListCtrl
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
         * @memberOf StoreListCtrl
         *
         * @description
         *   Opens a modal window with the module details
         *
         * @param {Object} item The item to detail.
         */
        $scope.showDetails = function(item) {
          var modal = $uibModal.open({
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

        $scope.$watch('type', function(nv, ov) {
          if (ov === nv) {
            return;
          }

          $scope.items = $scope[nv];
        }, true);

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
