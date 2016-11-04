(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  PurchaseListCtrl
     *
     * @requires $scope
     * @requires http
     *
     * @description
     *   Handles all actions in purchases listing.
     */
    .controller('PurchaseListCtrl', [
      '$scope', '$timeout', 'http', 'oqlEncoder',
      function($scope, $timeout, http, oqlEncoder) {
          /**
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = { epp: 10, orderBy: { updated: 'desc' }, page: 1 };

         /**
         * @function refresh
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Reloads the list.
         */
        $scope.list = function () {
          $scope.loading = true;

          oqlEncoder.configure({
            placeholder: {
              client: 'client ~ "[value]"',
              from: 'created > "[value]"',
              to: 'created < "[value]"'
            }
          });

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: 'backend_ws_purchases_list',
            params: { oql: oql }
          };

          http.get(route).then(function(response) {
            $scope.loading = false;
            $scope.items   = response.data.results;
            $scope.total   = response.data.total;
          }, function() {
              $scope.loading = false;
          });
        };

        // Reloads the list when filters change.
        $scope.$watch('criteria', function(nv, ov) {
          if ($scope.searchTimeout) {
            $timeout.cancel($scope.searchTimeout);
          }

          if (nv === ov) {
            return;
          }

          $scope.searchTimeout = $timeout(function() {
            $scope.list();
          }, 500);
        }, true);
      }
    ]);
})();
