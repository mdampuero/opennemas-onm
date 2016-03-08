(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  StoreCheckoutCtrl
     *
     * @requires $analytics
     * @requires $http
     * @requires $uibModal
     * @requires $scope
     * @requires messenger
     * @requires routing
     * @requires webStorage
     *
     * @description
     *   Controller to handle actions in checkout
     */
    .controller('StoreCheckoutCtrl', ['$analytics', '$http', '$uibModal', '$scope', '$timeout', 'messenger', 'routing', 'webStorage',
      function ($analytics, $http, $uibModal, $scope, $timeout, messenger, routing, webStorage) {
        /**
         * @memberOf StoreCheckoutCtrl
         *
         * @description
         *   The billing information.
         *
         * @type {Object}
         */
        $scope.billing = {
          country: 'ES'
        };

        /**
         * @memberOf StoreCheckoutCtrl
         *
         * @description
         *   Flag to edit billing information.
         *
         * @type {Boolean}
         */
        $scope.edit = false;

        /**
         * @memberOf StoreCheckoutCtrl
         *
         * @description
         *   The current step in the checkout wizard.
         *
         * @type {Boolean}
         */
        $scope.step = 1;

        /**
         * @memberOf StoreCheckoutCtrl
         *
         * @description
         *   Flag to know if current phone is valid.
         *
         * @type {Boolean}
         */
        $scope.validPhone = true;

        /**
         * @memberOf StoreCheckoutCtrl
         *
         * @description
         *   Flag to know if current VAT is valid.
         *
         * @type {Boolean}
         */
        $scope.validVat = true;

        /**
         * @memberOf StoreCheckoutCtrl
         *
         * @description
         *   The VAT tax to apply.
         *
         * @type {Boolean}
         */
        $scope.vatTax = 0;

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
            var id = e.id;
            if (!id) {
              id = e.uuid;
            }
            return id;
          });

          var data = { billing: $scope.billing, modules: modules };

          $http.post(url, data).success(function() {
            $scope.step = 4;
            $scope.cart = [];
            webStorage.local.remove('cart');
            $analytics.pageTrack('/store/checkout/done');
          }).error(function() {
            messenger.post({
              message: 'There was an error with your request',
              type: 'error'
            });
          });
        };

        /**
         * @function setStep
         * @memberOf StoreCheckoutCtrl
         *
         * @description
         *  Sets the step.
         */
        $scope.setStep = function(step) {
          $scope.step = step;
        };

        /**
         * @function removeFromCart
         * @memberOf StoreCheckoutCtrl
         *
         * @description
         *   Removes an item from cart.
         *
         * @param {Object} item  The item to remove.
         */
        $scope.removeFromCart = function(item) {
          $scope.cart.splice($scope.cart.indexOf(item), 1);
        };

        // Updates the edit flag when billing changes.
        $scope.$watch('billing', function(nv) {
          if (!nv || !nv.name) {
            $scope.edit       = true;
            $scope.validPhone = false;
            $scope.validVat   = false;
          }
        });

        // Updates the total when the cart changes
        $scope.$watch('cart', function(nv) {
          $scope.subtotal = 0;
          $scope.total    = 0;

          if (!nv || (nv instanceof Array && nv.length === 0)) {
            webStorage.local.remove('cart');
            return;
          }

          webStorage.local.set('cart', nv);

          for (var i = 0; i < nv.length; i++) {
            if (nv[i].price && nv[i].price.month) {
              $scope.subtotal += nv[i].price.month;
            }
          }

          $scope.vat   = ($scope.subtotal * $scope.vatTax) / 100;
          $scope.total = $scope.subtotal + $scope.vat;
        }, true);

        // Updates vat and total values when vat tax changes
        $scope.$watch('validVat', function(nv) {
          if (nv === true) {
            $scope.vat   = ($scope.subtotal * $scope.vatTax) / 100;
            $scope.total = $scope.subtotal + $scope.vat;
          }
        });

        // Updates the edit flag when billing changes.
        $scope.$watch('[billing.company, billing.country, billing.vat]', function() {
          if (!$scope.billing) {
            return;
          }

          $scope.vatTax = 0;

          // Individual customer
          if (!$scope.billing.company && $scope.billing.country &&
              $scope.taxes[$scope.billing.country]) {
            $scope.vatTax = $scope.taxes[$scope.billing.country].value;
            return;
          }

          // Spanish company
          if ($scope.billing.company && $scope.billing.country === 'ES' &&
              $scope.taxes[$scope.billing.country]) {
            $scope.vatTax = $scope.taxes[$scope.billing.country].value;
          }
        }, true);

        $scope.$watch('billing.country', function(nv, ov) {
          if (!nv) {
            return;
          }

          var url = routing.generate('backend_ws_store_check_phone',
              { country: $scope.billing.country, phone: $scope.billing.phone });

          $http.get(url).success(function() {
            $scope.validPhone = true;
          }).error(function() {
            $scope.validPhone = false;
          });

          url = routing.generate('backend_ws_store_check_vat',
              { country: $scope.billing.country, vat: $scope.billing.vat });

          $http.get(url).success(function() {
            $scope.validVat = true;
          }).error(function() {
            $scope.validVat = false;
          });
        }, true);

        // Updates the edit flag when billing changes.
        $scope.$watch('billing.phone', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if (!$scope.billing || !$scope.billing.country ||
              !$scope.billing.phone) {
            $scope.validPhone = false;
            return;
          }

          if ($scope.searchTimeout) {
            $timeout.cancel($scope.searchTimeout);
          }

          var url = routing.generate('backend_ws_store_check_phone',
              { country: $scope.billing.country, phone: $scope.billing.phone });

          $scope.searchTimeout = $timeout(function() {
            $http.get(url).success(function() {
              $scope.validPhone = true;
            }).error(function() {
              $scope.validPhone = false;
            });
          }, 500);
        }, true);

        // Updates the edit flag when billing changes.
        $scope.$watch('billing.vat', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if (!$scope.billing || !$scope.billing.country ||
              !$scope.billing.vat) {
            $scope.validVat = false;
            return;
          }

          if ($scope.searchTimeout) {
            $timeout.cancel($scope.searchTimeout);
          }

          var url = routing.generate('backend_ws_store_check_vat',
              { country: $scope.billing.country, vat: $scope.billing.vat });

          $scope.searchTimeout = $timeout(function() {
            $http.get(url).success(function() {
              $scope.validVat = true;
            }).error(function() {
              $scope.validVat = false;
            });
          }, 500);
        }, true);

        // Initialize the shopping cart from the webStorage
        if (webStorage.local.has('cart')) {
          $scope.cart = webStorage.local.get('cart');
        }
      }
    ]);
})();
