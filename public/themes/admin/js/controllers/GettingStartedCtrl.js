angular.module('BackendApp.controllers')
  /**
   * Handle actions for article inner.
  */
  .controller('GettingStartedCtrl', ['$http', '$scope', '$timeout', 'routing',
  function($http, $scope, $timeout, routing) {
    'use strict';

    $scope.step = 1;

    $scope.termsAccepted = true;

    /**
     * Sends a request to accepts/reject terms and conditions.
     */
    $scope.acceptTerms = function() {
      var url = routing.generate('admin_getting_started_accept_terms');

      $http.post(url, { accept : $scope.termsAccepted }).error(function() {
        $scope.termsAccepted = false;
      });
    };

    $scope.goToStep = function(step) {
      $scope.step = step;
      $timeout(function() {
        $('body').scrollTop(0);
      }, 250);
    };
  }
]);
