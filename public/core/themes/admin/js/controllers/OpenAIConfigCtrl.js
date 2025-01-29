(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  OpenAIConfigCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     * @requires messenger
     *
     * @description
     *   Provides actions to list notifications.
     */
    .controller('OpenAIConfigCtrl', [
      '$controller', '$scope', 'http', 'messenger', '$uibModal',
      function($controller, $scope, http, messenger, $uibModal) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf OpenAIConfigCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          checkApiKey: 'api_v1_backend_openai_check_apiKey',
          getConfig: 'api_v1_backend_openai_get_config',
          saveConfig: 'api_v1_backend_openai_save_config',
          uploadConfig: 'api_v1_backend_openai_upload_config',
          downloadConfig: 'api_v1_backend_openai_download_config',
        };

        $scope.message = {
          errorApiKey: 'Please enter a valid Secret Key'
        };

        $scope.needCheckApiKey = false;

        /**
         * @function init
         * @memberOf OpenAIConfigCtrl
         *
         * @description
         *   Initializes the form.
         */
        $scope.init = function() {
          http.get($scope.routes.getConfig).then(function(response) {
            $scope.settings = response.data;
            $scope.disableFlags('http');
          }, function() {
            $scope.disableFlags('http');
          });
        };

        /**
         * @function save
         * @memberOf OpenAIConfigCtrl
         *
         * @description
         *   Saves the configuration.
         */
        $scope.save = function() {
          if (!$scope.flags.http.checking) {
            $scope.flags.http.saving = true;
          }

          var data = $scope.settings;

          return http.put($scope.routes.saveConfig, data)
            .then(function(response) {
              if (!$scope.flags.http.checking) {
                $scope.disableFlags('http');
                messenger.post(response.data);
              }
            }, function(response) {
              if (!$scope.flags.http.checking) {
                $scope.disableFlags('http');
                messenger.post(response.data);
              }
            });
        };

        /**
         * @function check
         * @memberOf OpenAIConfigCtrl
         *
         * @description
         *   Checks the connection to the server.
         */
        $scope.checkApiKey = function() {
          if ($scope.settings.openai_service === 'custom' && $scope.needCheckApiKey) {
            const apiKey = $scope.settings.openai_credentials.apikey;

            if (!apiKey) {
              messenger.post($scope.message.errorApiKey, 'error');
            } else {
              if (!$scope.flags.http.checking) {
                $scope.flags.http.saving = true;
              }
              http.post($scope.routes.checkApiKey, { apiKey: apiKey })
                .then(function() {
                  if (!$scope.flags.http.checking) {
                    $scope.disableFlags('http');
                  }
                  $scope.save();
                }, function() {
                  if (!$scope.flags.http.checking) {
                    $scope.disableFlags('http');
                  }
                  messenger.post($scope.message.errorApiKey, 'error');
                });
            }
          } else {
            $scope.save();
          }
        };

        $scope.addRole = function() {
          const role = {
            name: '',
            prompt: ''
          };

          $scope.settings.onmai_roles.push(role);
        };

        $scope.removeRole = function(index) {
          $scope.settings.onmai_roles.splice(index, 1);
        };

        $scope.addTone = function() {
          const tone = {
            name: '',
            description: ''
          };

          $scope.settings.onmai_tones.push(tone);
        };

        $scope.removeTone = function(index) {
          $scope.settings.onmai_tones.splice(index, 1);
        };

        $scope.addInstruction = function() {
          const instruction = {
            type: 'Both',
            value: ''
          };

          $scope.settings.onmai_instructions.push(instruction);
        };

        $scope.removeInstruction = function(index) {
          $scope.settings.onmai_instructions.splice(index, 1);
        };

        $scope.$watch('settings.openai_credentials.apikey', function(ov) {
          if (typeof ov !== 'undefined') {
            $scope.needCheckApiKey = true;
          }
        });

        /**
         * @function openImportModal
         * @memberOf ThemeSettingCtrl
         *
         * @description
         *   Confirm import settings from JSON string.
         */
        $scope.openImportModal = function() {
          var modal = $uibModal.open({
            templateUrl: 'modal-import-settings',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {
                };
              },
              success: function() {
                return function(modal, template) {
                  const reader = new FileReader();

                  var route = {
                    name: $scope.routes.uploadConfig,
                  };

                  if (template.file.type !== 'application/json') {
                    return messenger.post('No es un fichero JSON Válido', 'error');
                  }

                  reader.readAsText(template.file);
                  reader.onload = function() {
                    var content = reader.result;

                    return http.put(route, { openai_config: content }).then(function(response) {
                      messenger.post(response.data);
                      $scope.init();
                    });
                  };
                };
              }
            }
          });
        };
      }
    ]);
})();
