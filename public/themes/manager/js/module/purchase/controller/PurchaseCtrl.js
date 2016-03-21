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
      '$filter', '$location', '$uibModal', '$scope', 'itemService', 'routing', 'messenger', 'data',
      function ($filter, $location, $uibModal, $scope, itemService, routing, messenger, data) {

        /**
         * @memberOf PurchaseCtrl
         *
         * @description
         *   The template parameters.
         *
         * @type {Object}
         */
        $scope.extra = data.extra;

        $scope.$on('$destroy', function() {
          $scope.purchase = null;
        });

        if (data.purchase) {
          $scope.purchase = data.purchase;

          $scope.subtotal = 0;
          $scope.tax      = 0;
          $scope.total    = 0;

          for (var i = 0; i < $scope.purchase.details.length; i++) {
            var line = $scope.purchase.details[i];

            $scope.tax   += line.unit_cost * line.quantity * (line.tax1_percent / 100);
            $scope.subtotal += line.unit_cost * line.quantity;
          }

          $scope.total = $scope.subtotal + $scope.tax;
        }
      }
    ]);
})();
