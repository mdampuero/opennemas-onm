(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  OnmAIModalCtrl.js
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
    .controller('OnmAIModalCtrl', [
      '$uibModalInstance', '$scope', '$q', 'routing', 'success', 'template', 'http', '$timeout', 'oqlEncoder', 'messenger',
      function($uibModalInstance, $scope, $q, routing, success, template, http, $timeout, oqlEncoder, messenger) {
        $scope.routes = {
          generateText: 'api_v1_backend_onmai_generate',
          translate:    'api_v1_backend_onmai_translate',
          saveTokens:   'api_v1_backend_onmai_tokens',
        };

        $scope.last_token_usage        = 0;
        $scope.last_words_generated    = 0;
        $scope.waiting                 = false;
        $scope.edit_input              = false;
        $scope.template                = template;
        $scope.displayMode             = $scope.template.AIFieldType === 'descriptions' || $scope.template.AIFieldType === 'bodies' ? 'textarea' : 'input';
        $scope.template.roleSelected   = null;
        $scope.template.promptSelected = null;
        $scope.template.toneSelected   = null;
        $scope.template.promptInput    = null;
        $scope.mode                    = $scope.template.input && $scope.template.input.trim() !== '' ? 'Edit' : 'New';
        $scope.error                   = false;
        $scope.showPrompt              = true;

        /**
         * @function init
         * @memberOf OnmAIModalCtrl.js
         *
         * @description
         *   Initializes the modal with default criteria and fetches a list of prompts from the server.
         */
        $scope.init = function() {
          if ($scope.mode === 'New' && typeof $scope.template.lastTemplate !== 'undefined') {
            $scope.template.input = $scope.template.lastTemplate.input;
          }

          $scope.criteria = {
            epp: 1000,
            field: $scope.template.AIFieldType,
            orderBy: { name: 'asc' },
            page: 1,
          };

          $scope.waiting = true;

          var oqlQuery = oqlEncoder.getOql($scope.criteria);

          http.get({ name: 'api_v1_backend_onmai_prompt_get_list', params: { oql: oqlQuery, field: $scope.template.AIFieldType } })
            .then(function(response) {
              $scope.prompts = response.data.items;
              $scope.extra = response.data.extra;
              $scope.setLocale();
            })
            .finally(function() {
              $scope.waiting = false;
            });
        };

        $scope.setLocale = function() {
          var exists = $scope.extra.languages.some(function(lang) {
            return lang.code === $scope.template.locale;
          });

          $scope.template.locale = exists ? $scope.template.locale : 'es_ES';
        };

        /**
         * @function continue
         * @memberOf OnmAIModalCtrl.js
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
         * @memberOf OnmAIModalCtrl.js
         *
         * @description
         *   Sends a request to generate suggested text based on user input and updates the template with the result.
         */
        $scope.generate = function() {
          $scope.error = false;
          if (!$scope.template) {
            return;
          }

          $scope.waiting = true;

          http.post($scope.routes.generateText, $scope.template)
            .then(function(response) {
              $scope.template.suggested_text = response.data.result;
              $scope.template.original_text  = $scope.template.input;
              $scope.last_token_usage        = response.data.tokens.total;
              $scope.last_words_generated    = response.data.words.total;
              $scope.template.step           = 2;
              $scope.setActiveText('suggested');
            })
            .catch(function(err) {
              $scope.error = true;
              messenger.post(err.data.error, 'error');
            })
            .finally(function() {
              $scope.waiting = false;
            });
        };

        $scope.translate = function() {
          $scope.error = false;
          if (!$scope.template) {
            return;
          }

          $scope.waiting = true;

          http.post($scope.routes.translate, {
            text: $scope.template.orVal,
            lang: $scope.template.locale,
            tone: $scope.template.toneSelected
          })
            .then(function(response) {
              $scope.template.suggested_text = response.data.result;
              $scope.template.original_text  = $scope.template.input;
              $scope.last_token_usage        = response.data.tokens.total;
              $scope.last_words_generated    = response.data.words.total;
              $scope.template.step           = 2;
              $scope.setActiveText('suggested');
            })
            .catch(function(err) {
              $scope.error = true;
              messenger.post(err.data.error, 'error');
            })
            .finally(function() {
              $scope.waiting = false;
            });
        };

        /**
         * @function close
         * @memberOf OnmAIModalCtrl.js
         *
         * @description
         *   Closes the modal, optionally returning a response.
         *
         * @param {Object} response - The response data to return when closing the modal.
         */
        $scope.close = function(response) {
          $uibModalInstance.close(response);
        };

        $scope.updateUserPrompt = function() {
          if ($scope.template.promptSelected) {
            $scope.template.roleSelected = $scope.extra.roles.find(function(obj) {
              return obj['name'] === $scope.template.promptSelected.role;
            });

            $scope.template.toneSelected = $scope.extra.tones.find(function(obj) {
              return obj['name'] === $scope.template.promptSelected.tone;
            });

            $scope.template.promptSelected = $scope.prompts.find(function(obj) {
              return obj === $scope.template.promptSelected;
            });
            $scope.template.promptInput = $scope.template.promptSelected.prompt;
            if ($scope.template && $scope.template.promptSelected.hasOwnProperty('instances')) {
              $scope.showPrompt = false;
            }
          } else {
            $scope.template.roleSelected = null;
            $scope.template.toneSelected = null;
            $scope.template.promptInput  = null;
            $scope.showPrompt            = true;
          }
        };

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
         * @memberOf OnmAIModalCtrl.js
         *
         * @description
         *   Dismisses the modal without returning any specific response.
         */
        $scope.dismiss = function() {
          $scope.close(true);
        };

        /**
         * @function back
         * @memberOf OnmAIModalCtrl.js
         *
         * @description
         *   Returns to the first step in the modal workflow.
         */
        $scope.back = function() {
          $scope.template.step = 1;
        };

        /**
         * @function $on('$destroy')
         * @memberOf OnmAIModalCtrl.js
         *
         * @description
         *   Cleans up the template data when the modal is destroyed.
         */
        $scope.$on('$destroy', function() {
          $scope.template = null;
        });

        /**
         * @function setActiveText
         * @memberOf OnmAIModalCtrl.js
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

        $scope.countWords = function(input) {
          if (!input || typeof input !== 'string') {
            return 0;
          }
          return input.trim().split(/\s+/).filter(function(word) {
            return word.length > 0;
          }).length;
        };

        $scope.filtroDinamico = function(item) {
          return item[item.field] === 'titles';
        };

        $scope.init();
      }
    ]);
})();
