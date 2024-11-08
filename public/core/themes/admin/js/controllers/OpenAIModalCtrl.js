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
     * @requires timeout
     *
     * @description
     *   Controller for News Agency listing.
     */
    .controller('OpenAIModalCtrl', [
      '$uibModalInstance', '$scope', '$q', 'routing', 'success', 'template', 'http', '$timeout', 'oqlEncoder',
      function($uibModalInstance, $scope, $q, routing, success, template, http, $timeout, oqlEncoder) {
        $scope.routes = {
          generateText: 'api_v1_backend_openai_generate',
          saveTokens: 'api_v1_backend_openai_tokens',
        };

        $scope.last_token_usage = 0;
        $scope.waiting = false;
        $scope.edit_context = false;
        $scope.template = template;

        /**
         * @function init
         * @memberOf OpenAIModalCtrl
         *
         * @description
         *   Initializes the modal with default criteria and fetches a list of prompts from the server.
         */
        $scope.init = function() {
          $scope.criteria = {
            epp: 1000,
            field: $scope.template.AIFieldType,
            orderBy: { name: 'asc' },
            page: 1,
          };

          $scope.waiting = true;
          var oqlQuery = oqlEncoder.getOql($scope.criteria);

          http.get({ name: 'api_v1_backend_openai_prompt_get_list', params: { oql: oqlQuery } })
            .then(function(response) {
              $scope.prompts = response.data.items;
            })
            .finally(function() {
              $scope.waiting = false;
            });
        };

        /**
         * @function continue
         * @memberOf OpenAIModalCtrl
         *
         * @description
         *   Proceeds to the next step in the modal's workflow by calling `generate` or `confirm`.
         */
        $scope.continue = function() {
          if ($scope.template.step === 1) {
            $scope.generate();
          } else {
            $scope.confirm();
          }
        };

        /**
         * @function generate
         * @memberOf OpenAIModalCtrl
         *
         * @description
         *   Sends a request to generate suggested text based on user input and updates the template with the result.
         */
        $scope.generate = function() {
          if (!$scope.template) {
            return;
          }

          $scope.waiting = true;

          http.post($scope.routes.generateText, $scope.template)
            .then(function(response) {
              $scope.template.suggested_text = response.data.message;
              $scope.last_token_usage = response.data.tokens.total_tokens;
              $scope.template.step = 2;
              $scope.setActiveText('suggested');
            })
            .finally(function() {
              $scope.waiting = false;
            });
        };

        /**
         * @function close
         * @memberOf OpenAIModalCtrl
         *
         * @description
         *   Closes the modal, optionally returning a response.
         *
         * @param {Object} response - The response data to return when closing the modal.
         */
        $scope.close = function(response) {
          $uibModalInstance.close(response);
        };

        /**
         * @function updateUserPrompt
         * @memberOf OpenAIModalCtrl
         *
         * @description
         *   Updates `user_prompt` and `context_prompt` in the template based on the selected prompt.
         */
        $scope.updateUserPrompt = function() {
          var selectedPrompt = $scope.prompts[$scope.template.promtSelected];

          if (selectedPrompt) {
            $scope.template.user_prompt = selectedPrompt.name + ': "' + $scope.template.original_text + '"';
            $scope.template.context_prompt = selectedPrompt.context;
          } else {
            $scope.template.user_prompt = '';
          }
        };

        /**
         * @function confirm
         * @memberOf OpenAIModalCtrl
         *
         * @description
         *   Executes the `success` function (if provided), handles the response, and closes the modal.
         */
        $scope.confirm = function() {
          if (!success || typeof success !== 'function') {
            return $scope.close(true);
          }

          $q.when(success($uibModalInstance, $scope.template))
            .then(function(response) {
              $scope.resolve(response, true);
            })
            .catch(function() {
              $uibModalInstance.dismiss('error');
            });
        };

        /**
         * @function resolve
         * @memberOf OpenAIModalCtrl
         *
         * @description
         *   Processes response data and closes the modal based on success status.
         *
         * @param {Object} response - The response data from the confirm step.
         * @param {boolean} success - Indicates if the operation was successful.
         */
        $scope.resolve = function(response, success) {
          var result = response && Object.keys(response).length > 0 ? {
            data: response.data,
            headers: response.headers,
            status: response.status,
            success: success
          } : success;

          $scope.close(result);
        };

        /**
         * @function dismiss
         * @memberOf OpenAIModalCtrl
         *
         * @description
         *   Dismisses the modal without returning any specific response.
         */
        $scope.dismiss = function() {
          $scope.close(true);
        };

        /**
         * @function back
         * @memberOf OpenAIModalCtrl
         *
         * @description
         *   Returns to the first step in the modal workflow.
         */
        $scope.back = function() {
          $scope.template.step = 1;
        };

        /**
         * @function $on('$destroy')
         * @memberOf OpenAIModalCtrl
         *
         * @description
         *   Cleans up the template data when the modal is destroyed.
         */
        $scope.$on('$destroy', function() {
          $scope.template = null;
        });

        /**
         * @function setActiveText
         * @memberOf OpenAIModalCtrl
         *
         * @description
         *   Sets the active text type (e.g., "suggested") and updates the template's response with the selected text.
         *
         * @param {string} type - The type of text to activate and display.
         */
        $scope.setActiveText = function(type) {
          $scope.activeText = type;
          $scope.template.response = $scope.template[type + '_text'];
        };

        // Call init function automatically upon controller load
        $scope.init();
      }
    ]);
})();
