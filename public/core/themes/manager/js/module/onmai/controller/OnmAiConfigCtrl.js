(function() {
  'use strict';

  angular.module('ManagerApp.controllers')

    /**
     * @ngdoc controller
     * @name  OnmAiConfigCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $timeout
     * @requires http
     * @requires messenger
     *
     * @description
     *   Handles all actions in prompt.txt listing.
     */
    .controller('OnmAiConfigCtrl', [
      '$controller', '$scope', '$timeout', 'http', 'messenger', '$uibModal',
      function($controller, $scope, $timeout, http, messenger, $uibModal) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope: $scope,
          $timeout: $timeout
        }));

        $scope.currentTab = 'openai';
        $scope.allModels = [];
        $scope.modelsSuggested = {
          openai: [],
          deepseek: [],
          gemini: [],
          mistralai: [],
        };

        $scope.addModel = function() {
          if ($scope.onmai_settings.engines[$scope.currentTab].models == '') {
            $scope.onmai_settings.engines[$scope.currentTab].models = [];
          }
          $scope.onmai_settings.engines[$scope.currentTab].models.push({
            cost_input_tokens: 0,
            cost_output_tokens: 0,
            id: '',
            sale_input_tokens: 0,
            sale_output_tokens: 0,
            params: []
          });
        };

        $scope.selectTab = function(currentTab) {
          $scope.currentTab = currentTab;
        };

        $scope.saveActiveItems = function() {
          return http.put('manager_ws_onmai_config_save', {
            onmai_settings: $scope.onmai_settings
          })
            .then(function(response) {
              $scope.getModels();
              messenger.post(response.data);
            }, function(response) {
              messenger.post(response.data);
            });
        };

        $scope.removeModel = function(index) {
          $scope.onmai_settings.engines[$scope.currentTab].models.splice(index, 1);
        };

        $scope.getModels = function() {
          $scope.allModels = [];
          for (var engineKey in $scope.onmai_settings.engines) {
            if ($scope.onmai_settings.engines.hasOwnProperty(engineKey)) {
              var engine = $scope.onmai_settings.engines[engineKey];

              for (var i = 0; i < engine.models.length; i++) {
                var model = angular.copy(engine.models[i]);

                $scope.allModels.push({
                  id: engineKey + '_' + model.id,
                  name: engineKey + ' - ' + model.id
                });
              }
            }
          }
        };

        $scope.addParam = function(item) {
          if (typeof item.params === 'undefined' || item.params === '') {
            item.params = [];
          }
          item.params.push({
            key: '',
            value: ''
          });
        };

        $scope.removeParam = function(item, indexToRemove) {
          item.params.splice(indexToRemove, 1);
        };

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
                    name: 'manager_ws_onmai_config_upload',
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

        $scope.init = function() {
          $scope.loading = 1;
          var route = {
            name: 'manager_ws_onmai_config'
          };

          http.get(route).then(function(response) {
            $scope.onmai_settings = response.data.onmai_settings;
            $scope.loading = 0;
            $scope.getModels();
          });

          var route = {
            name: 'manager_ws_onmai_modelsSuggested'
          };

          http.get(route).then(function(response) {
            $scope.modelsSuggested = response.data;
          });
        };
        $scope.init();
      }
    ]);
})();

