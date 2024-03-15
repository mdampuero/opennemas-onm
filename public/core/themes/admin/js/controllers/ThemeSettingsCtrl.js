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
          logo_enabled: false,
          site_color: '',
          site_color_secondary: '',
          theme_skin: 'default'
        };

        $scope.routes = {
          saveConfig: 'api_v1_backend_settings_theme_save',
          getConfig: 'api_v1_backend_settings_theme_list',
          importConfig: 'api_v1_backend_settings_theme_import',
          restoreDefault: 'api_v1_backend_settings_theme_restore'
        };

        /**
         * @function removeFile
         * @memberOf SettingsCtrl
         *
         * @description
         *   Removes a file from settings.
         *
         * @param {String} name The file name.
         */
        $scope.removeFile = function(name) {
          $scope.settings[name] = null;
        };

        // Updates data to send to server when related contents change
        $scope.$watch('[ settings.logo_default, settings.logo_simple, settings.logo_favico, settings.logo_embed ]', function(nv) {
          if (nv[0] && isNaN(nv[0])) {
            $scope.settings.logo_default =  parseInt(nv[0].pk_content);
          }

          if (nv[1] && isNaN(nv[1])) {
            $scope.settings.logo_simple =  parseInt(nv[1].pk_content);
          }

          if (nv[2] && isNaN(nv[2])) {
            $scope.settings.logo_favico =  parseInt(nv[2].pk_content);
          }

          if (nv[3] && isNaN(nv[3])) {
            $scope.settings.logo_embed =  parseInt(nv[3].pk_content);
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
        $scope.openRestoreModal = function() {
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
