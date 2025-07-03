(function() {
  'use strict';

  angular.module('ManagerApp.controllers')

    /**
     * @ngdoc controller
     * @name  StorageConfigCtrl
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
    .controller('StorageConfigCtrl', [
      '$controller', '$scope', '$timeout', 'http', 'messenger', '$uibModal',
      function($controller, $scope, $timeout, http, messenger, $uibModal) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope: $scope,
          $timeout: $timeout
        }));

        $scope.save = function() {
          return http.put('manager_ws_storage_config_save', {
            storage_settings: $scope.storage_settings
          })
            .then(function(response) {
              messenger.post(response.data);
            }, function(response) {
              messenger.post(response.data);
            });
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
            name: 'manager_ws_storage_config'
          };

          http.get(route).then(function(response) {
            $scope.storage_settings = response.data.storage_settings;
            $scope.loading = 0;
          });
        };
        $scope.init();
      }
    ]);
})();

