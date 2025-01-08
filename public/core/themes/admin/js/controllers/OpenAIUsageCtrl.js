(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  OpenAIUsageCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     * @requires messenger
     *
     * @description
     *   Provides actions to list notifications.
     */
    .controller('OpenAIUsageCtrl', [
      '$controller', '$scope', 'http',
      function($controller, $scope, http) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf OpenAIUsageCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          getPricing: 'api_v1_backend_openai_get_pricing',
        };

        /**
         * @function init
         * @memberOf OpenAIUsageCtrl
         *
         * @description
         *   Initializes the form.
         */
        $scope.init = function() {
          http.get($scope.routes.getPricing).then(function(response) {
            $scope.tokens = response.data.tokens;
            $scope.prices = response.data.prices;
            $scope.total  = response.data.total;
            $scope.disableFlags('http');
          }, function() {
            $scope.disableFlags('http');
          });
        };
      }
    ]);
})();
