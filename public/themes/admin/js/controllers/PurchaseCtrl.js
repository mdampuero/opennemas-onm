(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  PurchaseCtrl
     *
     * @requires $location
     * @requires $uibModal
     * @requires $scope
     * @requires itemService
     * @requires routing
     * @requires messenger
     * @requires data
     *
     * @description
     *   Handles actions for purchase edition form
     */
    .controller('PurchaseCtrl', [
      '$scope', 'routing', 'http', 'messenger', 'webStorage',
      function ($scope, routing, http, messenger, webStorage) {
        /**
         * @function getPurchase
         * @memberOf PurchaseCtrl
         *
         * @description
         *   Gets a purchase.
         *
         * @param {Integer} id The purchase id.
         */
        $scope.getPurchase = function(id) {
          $scope.loading = true;

          var route = { name: 'backend_ws_purchase_show', params: { id: id } };

          http.get(route).then(function(response) {
            $scope.loading  = false;
            $scope.purchase = response.data.purchase;
            $scope.extra    = response.data.extra;
          }, function() {
            $scope.loading = false;
            $scope.error   = true;
          });
        };

        /**
         * @function getNotes
         * @memberOf CheckoutCtrl
         *
         * @description
         *   Returns the invoice notes.
         */
        $scope.getNotes = function () {
          return $scope.purchase.notes.split('\n').join('<br>');
        };

        /**
         * @function getTerms
         * @memberOf CheckoutCtrl
         *
         * @description
         *   Returns the invoice terms.
         */
        $scope.getTerms = function () {
          return $scope.purchase.terms.split('\n').join('<br>');
        };

        $scope.$on('$destroy', function() {
          $scope.purchase = null;
          $scope.extra    = null;
        });

        $scope.$watch('purchase', function(nv) {
          if (!nv) {
            return;
          }

          $scope.subtotal = 0;
          $scope.tax      = 0;
          $scope.total    = 0;

          for (var i = 0; i < $scope.purchase.details.length; i++) {
            var line = $scope.purchase.details[i];

            $scope.tax      += line.unit_cost * line.quantity * (line.tax1_percent / 100);
            $scope.subtotal += line.unit_cost * line.quantity;
          }

          $scope.total = $scope.subtotal + $scope.tax;
        });
      }
    ]);
})();
