(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  GeneralSettingsCtrl
     *
     * @requires $controller
     * @requires $scope
     */
    .controller('AppearanceSettingsCtrl', [
      '$controller', '$scope',
      function($controller, $scope) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('SettingsCtrl', { $scope: $scope }));
        $scope.settings = {
          site_color: '',
          site_color_secondary: '',
          logo_enabled: false,
          cookies: '',
          cookies_hint_url: '',
          cmp_type: '',
          cmp_id: '',
          cmp_id_amp: '',
          cmp_apikey: '',
          browser_update: false,
          items_per_page: '',
          items_in_blog: '',
          elements_in_rss: ''
        };

        $scope.routes = {
          saveConfig: 'api_v1_backend_settings_appearance_save',
          getConfig:  'api_v1_backend_settings_appearance_list'
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
      }
    ]);
})();
