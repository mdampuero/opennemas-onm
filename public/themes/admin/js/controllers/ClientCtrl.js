(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  ClientCtrl
     *
     * @requires $http
     * @requires $rootScope
     * @requires $scope
     * @requires $timeout
     * @requires messenger
     * @requires routing
     *
     * @description
     *   Handles action to save new clients.
     */
    .controller('ClientCtrl', [
      '$http', '$rootScope', '$scope', '$timeout', 'messenger', 'routing',
      function($http, $rootScope, $scope, $timeout, messenger, routing) {
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
        $scope.validVatNumber = true;

        /**
         * @memberOf ClientCtrl
         *
         * @description
         *   The VAT tax to apply.
         *
         * @type {Boolean}
         */
        $scope.vatTax = 0;

        /**
         * @function isVatNumberRequired
         * @memberOf ClientCtrl
         *
         * @description
         *   Checks if the vat identification number is required.
         *
         * @return {Boolean} True if the vat number is required. Otherwise,
         *                   returns false.
         */
        $scope.isVatNumberRequired = function() {
          // Person or company in Spain
          if ($scope.client && $scope.client.country === 'ES') {
            return true;
          }

          // Company in EU
          if ($scope.client && $scope.client.company &&
              $scope.taxes[$scope.client.country]) {
            return true;
          }

          // Person in EU or company and persons outside EU
          return false;
        };

        /**
         * @function confirm
         * @memberOf ClientCtrl
         *
         * @description
         *   Saves or updates a client.
         */
        $scope.confirm = function () {
          if (!$scope.clientForm.$dirty) {
            $rootScope.$broadcast('client-saved', $scope.client);
            return;
          }


          if ($scope.client.id) {
            $scope.update();
            return;
          }

          $scope.save();
        };

        /**
         * @function save
         * @memberOf ClientCtrl
         *
         * @description
         *   Saves a client.
         */
        $scope.save = function () {
          $scope.loading = true;

          var url = routing.generate('backend_ws_client_save');

          $http.post(url, $scope.client).then(function(response) {
            $scope.loading = false;

            if (response.data) {
              $scope.client.id = response.data;
            }

            $rootScope.$broadcast('client-saved', $scope.client);
          }, function(response) {
            $scope.loading = false;
            messenger.post(response);
          });
        };

        /**
         * @function update
         * @memberOf ClientCtrl
         *
         * @description
         *   Updates a client.
         */
        $scope.update = function () {
          $scope.loading = true;

          var url = routing.generate('backend_ws_client_update',
              { id: $scope.client.id });

          $http.put(url, $scope.client).then(function() {
            $scope.loading = false;
            $rootScope.$broadcast('client-saved', $scope.client);
          }, function(response) {
            $scope.loading = false;
            messenger.post(response);
          });
        };

        // Updates the edit flag when billing changes.
        $scope.$watch('[client.company, client.country, client.vat_number]', function() {
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

        // Updates the edit flag when billing changes.
        $scope.$watch('client.vat_number', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if ($scope.searchTimeout) {
            $timeout.cancel($scope.searchTimeout);
          }

          var url = routing.generate('backend_ws_store_check_vat',
              { country: $scope.client.country, vat: $scope.client.vat_number });

          $scope.searchTimeout = $timeout(function() {
            $http.get(url).then(function() {
              $scope.validVatNumber = true;
            }, function() {
              $scope.validVatNumber = false;
            });
          }, 500);
        }, true);
    }]);
})();
