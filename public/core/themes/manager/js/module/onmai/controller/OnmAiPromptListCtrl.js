(function() {
  'use strict';

  angular.module('ManagerApp.controllers')

    /**
     * @ngdoc controller
     * @name  OnmAiPromptListCtrl
     *
     * @requires $controller
     * @requires $location
     * @requires $scope
     * @requires $timeout
     * @requires $uibModal
     * @requires http
     * @requires messenger
     * @requires oqlEncoder
     * @requires webStorage
     *
     * @description
     *   Handles all actions in prompt.txt listing.
     */
    .controller('OnmAiPromptListCtrl', [
      '$controller', '$location', '$scope', '$timeout', '$uibModal', 'http', 'messenger', 'oqlDecoder', 'oqlEncoder', 'webStorage',
      function($controller, $location, $scope, $timeout, $uibModal, http, messenger, oqlDecoder, oqlEncoder, webStorage) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope: $scope,
          $timeout: $timeout
        }));

        $scope.columns = {
          collapsed: 1,
          selected: [ 'name', 'instances' ]
        };

        $scope.criteria = { epp: 25, page: 1 };

        $scope.routes = {
          configGet: 'manager_ws_onmai_prompt_config',
          configSave: 'manager_ws_onmai_prompt_config_save',
          configUpload: 'manager_ws_onmai_prompt_config_upload',
          list: 'manager_onmai_prompt_list'
        };

        $scope.settings = {
          onmai_roles: [],
          onmai_tones: [],
          onmai_instructions: []
        };

        $scope.selectTab = function(tab) {
          if (tab !== 'prompts') {
            $scope.init();
          }
        };

        $scope.delete = function(item) {
          var modal = $uibModal.open({
            templateUrl: '/managerws/template/onmai:prompt:modal.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {};
              },
              success: function() {
                return function(modalWindow) {
                  var route = {
                    name: 'manager_ws_onmai_prompt_delete',
                    params: { id: item.id }
                  };

                  http.delete(route).then(function(response) {
                    modalWindow.close({ data: response.data, success: true });
                  }, function(response) {
                    modalWindow.close({ data: response.data, success: false });
                  });
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.list();
            }
          });
        };

        $scope.deleteSelected = function() {
          var modal = $uibModal.open({
            templateUrl: '/managerws/template/onmai:prompt:modal.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { selected: $scope.selected.items.length };
              },
              success: function() {
                return function(modalWindow) {
                  var route = 'manager_ws_onmai_prompt_batch_delete';
                  var data = { ids: $scope.selected.items };

                  http.delete(route, data).then(function(response) {
                    modalWindow.close({ data: response.data, success: true });
                  }, function(response) {
                    modalWindow.close({ data: response.data, success: false });
                  });
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.selected = { all: false, items: [] };
              $scope.list();
            }
          });
        };

        $scope.list = function() {
          $scope.loading = 1;

          oqlEncoder.configure({
            placeholder: {
              name: 'name ~ "[value]" or instances ~ "[value]"',
            }
          });

          var oql = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: 'manager_ws_onmai_prompt_list',
            params: { oql: oql }
          };

          $location.search('oql', oql);

          http.get(route).then(function(response) {
            $scope.loading = 0;
            $scope.items = response.data.results;
            $scope.total = response.data.total;
            $scope.extra = response.data.extra;

            // Scroll top
            $('body').animate({ scrollTop: '0px' }, 1000);
          });
        };

        $scope.resetFilters = function() {
          $scope.criteria = { epp: 25, page: 1 };
        };

        $scope.$watch('columns', function(newValues, oldValues) {
          if (newValues !== oldValues) {
            webStorage.local.set('prompt-columns', $scope.columns);
          }
        }, true);

        if (webStorage.local.get('prompt-columns')) {
          $scope.columns = webStorage.local.get('prompt-columns');
        }

        oqlDecoder.configure({
          ignore: ['prompt_lines', 'instances'],
          map: { name: 'name' }
        });

        if ($location.search().oql) {
          $scope.criteria = oqlDecoder.decode($location.search().oql);
        }

        $scope.list();

        /**
         *
         * @returns Configs
         */
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
            field: 'all',
            value: ''
          };

          $scope.settings.onmai_instructions.push(instruction);
        };

        $scope.removeInstruction = function(index) {
          $scope.settings.onmai_instructions.splice(index, 1);
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

        $scope.save = function() {
          var data = $scope.settings;

          return http.put($scope.routes.configSave, data)
            .then(function(response) {
              messenger.post(response.data);
            }, function(response) {
              messenger.post(response.data);
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
      }
    ]);
})();

