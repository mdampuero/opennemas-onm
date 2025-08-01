(function() {
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
    .controller('GettingStartedCtrl', [
      '$http', '$scope', '$timeout', 'routing',
      function($http, $scope, $timeout, routing) {
        $scope.step = 1;
        $scope.previous = 0;

        /**
         * @function acceptTerms
         * @memberOf GettingStartedCtrl
         *
         * @description
         *   Accepts/rejects the terms and conditions.
         */
        $scope.acceptTerms = function() {
          var url = routing.generate('backend_ws_getting_started_accept_terms');

          $http.put(url, { accept: $scope.termsAccepted }).then(null, function() {
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
          if (step > 2 && !$scope.termsAccepted) {
            $scope.warning = true;

            return;
          }

          $scope.previous = $scope.step;
          $scope.step     = step;

          $scope.termsWarning = false;

          $timeout(function() {
            $('body').scrollTop(0);
          }, 250);
        };
      }
    ]);
})();
