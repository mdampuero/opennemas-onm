(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  GettingStartedCtrl
     *
     * @requires $http
     * @requires $scope
     * @requires $timeout
     * @requires routing
     *
     * @description
     *   Handle actions for getting started.
     */
    .controller('GettingStartedCtrl', ['$http', '$scope', '$timeout', 'routing',
      function($http, $scope, $timeout, routing) {
        $scope.step = 1;

        /**
         * @function acceptTerms
         * @memberOf GettingStartedCtrl
         *
         * @description
         *   Accepts/rejects the terms and conditions.
         */
        $scope.acceptTerms = function() {
          var url = routing.generate('backend_ws_getting_started_accept_terms');

          $http.put(url, { accept : $scope.termsAccepted }).error(function() {
            $scope.termsAccepted = false;
          });
        };

        /**
         * @function goToStep
         * @memberOf GettingStartedCtrl
         *
         * @description
         *   Jumps to the given step
         *
         * @param {integer} step The step to jump to.
         */
        $scope.goToStep = function(step) {
          $scope.step = step;
          $timeout(function() {
            $('body').scrollTop(0);
          }, 250);
        };

        /**
         * @function savePaymentInfo
         * @memberOf GettingStartedCtrl
         *
         * @description
         *   Saves the payment information.
         */
        $scope.savePaymentInfo = function() {
          var url = routing.generate('backend_ws_getting_started_save_payment_info');

          $http.put(url, { billing : $scope.billing });
        };

      }
  ]);
})();
