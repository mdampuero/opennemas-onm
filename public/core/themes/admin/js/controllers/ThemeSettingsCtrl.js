(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ThemeSettingsCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $uibModal
     * @requires http
     * @requires messenger
     */
    .controller('ThemeSettingsCtrl', [
      '$controller', '$scope', '$uibModal', 'http', 'messenger',
      function($controller, $scope, $uibModal, http, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('SettingsCtrl', { $scope: $scope }));

        /**
         * @memberOf ThemeSettingsCtrl
         *
         * @description
         *  The settings object with default values.
         *
         * @type {Object}
         */
        $scope.settings = {
          theme_skin: 'default'
        };

        $scope.routes = {
          saveConfig: 'api_v1_backend_settings_theme_save',
          getConfig: 'api_v1_backend_settings_theme_list',
          importConfig: 'api_v1_backend_settings_theme_import',
          restoreDefault: 'api_v1_backend_settings_theme_restore'
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
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {
                };
              },
              success: function() {
                return function(modal, template) {
                  var route = {
                    name: $scope.routes.importConfig,
                  };

                  return http.put(route, { theme_settings: template.settings });
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.list().then(function() {
                $scope.selected = { all: false, items: [] };
              });
            }
          });
        };

        /**
         * @function openRestoreModal
         * @memberOf ThemeSettingCtrl
         *
         * @description
         *   Confirm restore settings to default value.
         */
        $scope.openRestoreModal = function(downloadRoute) {
          var modal = $uibModal.open({
            templateUrl: 'modal-restore-settings',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {
                };
              },
              success: function() {
                return function() {
                  var route = {
                    name: $scope.routes.restoreDefault,
                  };

                  window.location = downloadRoute;
                  return http.put(route);
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.list().then(function() {
                $scope.selected = { all: false, items: [] };
              });
            }
          });
        };
      }
    ]);
})();
