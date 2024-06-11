(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  OpenAIModalCtrl
     *
     * @requires $uibModalInstance
     * @requires $scope
     * @requires routing
     * @requires success
     * @requires template
     *
     * @description
     *   Controller for News Agency listing.
     */
    .controller('OpenAIModalCtrl', [
      '$uibModalInstance', '$scope', '$q', 'routing', 'success', 'template', 'http',
      function($uibModalInstance, $scope, $q, routing, success, template, http) {
        /**
         * @memberOf OpenAIModalCtrl
         *
         * @description
         *   The routing service.
         *
         * @type {Object}
         */
        $scope.routing = routing;

        $scope.routes = {
          generateText: 'api_v1_backend_openai_generate',
          saveTokens:   'api_v1_backend_openai_tokens',
        };

        $scope.last_token_usage = 0;
        $scope.showTokens = false;
        $scope.showResult = false;
        $scope.waiting = false;

        /**
         * @memberOf OpenAIModalCtrl
         *
         * @description
         *  The information provided by the controller which open the modal
         *  window.
         *
         * @type {Object}
         */
        $scope.template = template;

        $scope.generate = function() {
          $scope.waiting = true;

          http.post($scope.routes.generateText, $scope.template)
            .then(function(response) {
              console.log(response.data);
              $scope.template.response = response.data.message;
              $scope.last_token_usage = response.data.tokens.total_tokens;

              $scope.showResult = true;
              $scope.showTokens = true;
              $scope.waiting = false;

              setTimeout(function() {
                $scope.showTokens = false;
              }, 3000);
            }, function(response) {
              $scope.showError = true;
              setTimeout(function() {
                $scope.showError = false;
              }, 3000);
              $scope.waiting = false;
            });
        };

        /**
         * @function close
         * @memberOf OpenAIModalCtrl
         *
         * @description
         *   Closes the modal window returning the provided response to the
         *   controller which opened the modal window.
         *
         * @param {Object} response The response to return to the main
         *                          controller.
         */
        $scope.close = function(response) {
          $uibModalInstance.close(response);
        };

        /**
         * @function confirm
         * @memberOf OpenAIModalCtrl
         *
         * @description
         *   Executes the success callback and closes the window returning the
         *   response from the callback.
         */
        $scope.confirm = function() {
          $scope.loading = 1;

          if (!success || typeof success !== 'function') {
            $uibModalInstance.close(true);
            return;
          }

          $q.when(success($uibModalInstance, $scope.template))
            .then(function(response) {
              $scope.resolve(response, true);
            }, function(response) {
              $scope.resolve(response, false);
            });
        };

        $scope.resolve = function(response, success) {
          $scope.loading = 0;

          if (!response || Object.keys(response) > 0) {
            $uibModalInstance.close(success);
            return;
          }

          $uibModalInstance.close({
            data: response.data,
            headers: response.headers,
            status: response.status,
            success: success
          });
        };

        /**
         * @function dismiss
         * @memberOf OpenAIModalCtrl
         *
         * @description
         *   Closes the modal window without returning any response.
         */
        $scope.dismiss = function() {
          $uibModalInstance.dismiss();
        };

        // Changes step on client saved
        $scope.$on('client-saved', function(event, args) {
          $scope.client = args;
          $scope.template.step = 2;
        });

        // Frees up memory before controller destroy event
        $scope.$on('$destroy', function() {
          $scope.template = null;
        });
      }
    ]);
})();
