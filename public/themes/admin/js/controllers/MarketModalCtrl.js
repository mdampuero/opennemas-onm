(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  MarketModalCtrl
     *
     * @requires $http
     * @requires $modalInstance
     * @requires $scope
     * @requires routing
     * @requires template
     *
     * @description
     *   description
     */
    .controller('MarketModalCtrl', ['$http', '$modalInstance', '$scope', 'routing', 'template',
      function ($http, $modalInstance, $scope, routing, template) {
        /**
         * The current step.
         *
         * @type {Number}
         */
        $scope.step = 1;

        /**
         * The template parameters.
         * @type {Object}
         */
        $scope.template = template;

        /**
         * @function back
         * @memberOf MarketModalCtrl
         *
         * @description
         *   Decreases the current step value.
         */
        $scope.back = function() {
          $scope.step--;
        }

        /**
         * @function close
         * @memberOf MarketModalCtrl
         *
         * @description
         *   Closes the current modal
         */
        $scope.close = function() {
          $modalInstance.close(true);
        };

        /**
         * @function confirm
         * @memberOf MarketModalCtrl
         *
         * @description
         *   Confirms and executes the confirmed action.
         */
        $scope.confirm = function() {
          $scope.saving = true;
          var url = routing.generate('backend_ws_market_checkout');
          var data = $scope.template.cart.map(function(e) {
            return e.id;
          });

          $http.post(url, { modules: data }).success(function(response) {
            $scope.next();
          }).error(function() {
            $modalInstance.close(false);
          });
        };

        /**
         * @function dismiss
         * @memberOf MarketModalCtrl
         *
         * @description
         *   Dismiss the current modal
         */
        $scope.dismiss = function() {
          $modalInstance.dismiss();
        };

        /**
         * @function next
         * @memberOf MarketModalCtrl
         *
         * @description
         *   Increases the current step value.
         */
        $scope.next = function() {
          $scope.step++;
        }

        /**
         * @function removeFromCart
         * @memberOf MarketModalCtrl
         *
         * @description
         *   Removes an item from cart.
         *
         * @param {Object} item  The item to remove.
         * @param {Object} event The click event object.
         */
        $scope.removeFromCart = function(item) {
          $scope.template.cart.splice($scope.template.cart.indexOf(item), 1);
        };

        // Frees up memory before controller destroy event
        $scope.$on('$destroy', function() {
          $scope.template = null;
        });

        // Updates the total when the cart changes
        $scope.$watch('template.cart', function() {
          $scope.template.total = 0;

          for (var i = 0; i < $scope.template.cart.length; i++) {
            if ($scope.template.cart[i].price &&
                $scope.template.cart[i].price.month) {
              $scope.template.total += $scope.template.cart[i].price.month;
            }
          }
        }, true);
      }
    ]);
})();
