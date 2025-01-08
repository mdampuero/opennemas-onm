(function() {
  'use strict';

  angular.module('ManagerApp.controllers')

    /**
     * @ngdoc controller
     * @name  PromptConfigCtrl
     *
     * @requires $filter
     * @requires $location
     * @requires $scope
     * @requires http
     * @requires routing
     * @requires messenger
     * @requires data
     *
     * @description
     *   Handles actions for instance edition form
     */
    .controller('PromptConfigCtrl', [
      '$scope', 'http', 'messenger', '$uibModal',
      function($scope, http, messenger, $uibModal) {
        $scope.routes = {
          configGet:    'manager_ws_prompt_config',
          configSave:   'manager_ws_prompt_config_save',
          configUpload: 'manager_ws_prompt_config_upload',
          list:         'manager_prompt_list'
        };

        $scope.save = function() {
          var data = $scope.settings;

          return http.put($scope.routes.configSave, data)
            .then(function(response) {
              messenger.post(response.data);
            }, function(response) {
              messenger.post(response.data);
            });
        };

        $scope.settings = {
          openai_roles: [],
          openai_tones: [],
          openai_instructions: []
        };

        $scope.addRole = function() {
          const role = {
            name: '',
            prompt: ''
          };

          $scope.settings.openai_roles.push(role);
        };

        $scope.removeRole = function(index) {
          $scope.settings.openai_roles.splice(index, 1);
        };

        $scope.addTone = function() {
          const tone = {
            name: '',
            description: ''
          };

          $scope.settings.openai_tones.push(tone);
        };

        $scope.removeTone = function(index) {
          $scope.settings.openai_tones.splice(index, 1);
        };

        $scope.addInstruction = function() {
          const instruction = {
            type: 'Both',
            field: 'all',
            value: ''
          };

          $scope.settings.openai_instructions.push(instruction);
        };

        $scope.removeInstruction = function(index) {
          $scope.settings.openai_instructions.splice(index, 1);
        };

        $scope.init = function() {
          var route = {
            name: $scope.routes.configGet
          };

          return http.get(route).then(function(response) {
            $scope.settings = response.data;
          }, function() {
            $scope.item = {};
          });
        };

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
            controller: 'ModalImportCtrl',
            resolve: {
              template: function() {
                return {
                };
              },
              success: function() {
                return function(modal, template) {
                  const reader = new FileReader();

                  var route = {
                    name: $scope.routes.configUpload,
                  };

                  if (template.file.type !== 'application/json') {
                    return messenger.post('No es un fichero JSON Válido', 'error');
                  }

                  reader.readAsText(template.file);
                  reader.onload = function() {
                    var content = reader.result;

                    return http.put(route, { config: content }).then(function(response) {
                      modal.close();
                      messenger.post(response.data);
                      $scope.init();
                    });
                  };
                };
              }
            }
          });
        };

        $scope.init();
      }
    ]);
})();

