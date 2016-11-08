(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  PurchaseCtrl
     *
     * @requires $filter
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
      '$filter', '$location', '$uibModal', '$routeParams', '$scope', 'itemService', 'routing', 'messenger', 'webStorage',
      function ($filter, $location, $uibModal, $routeParams, $scope, itemService, routing, messenger, webStorage) {
        /**
         * @memberOf PurchaseCtrl
         *
         * @description
         *   The authorization token.
         *
         * @type {String}
         */
        $scope.token = webStorage.local.get('token');

        /**
         * @function getNotes
         * @memberOf CheckoutCtrl
         *
         * @description
         *   Returns the invoice notes.
         */
        $scope.getNotes = function () {
          return $scope.purchase.notes.split('\n').join('<br>');
        }
        /**
         * @function getTerms
         * @memberOf CheckoutCtrl
         *
         * @description
         *   Returns the invoice terms.
         */
        $scope.getTerms = function () {
          return $scope.purchase.terms.split('\n').join('<br>');
        }

        $scope.$on('$destroy', function() {
          $scope.purchase = null;
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

        if ($routeParams.id) {
          itemService.show('manager_ws_purchase_show', $routeParams.id).then(
            function(response) {
              $scope.purchase = response.data.purchase;
              $scope.instance = response.data.instance;
              $scope.extra    = response.data.extra;
            }
          );
        }
      }
    ]);
})();
