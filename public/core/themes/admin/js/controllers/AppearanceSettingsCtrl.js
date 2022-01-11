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
      '$controller', '$scope', 'cleaner',
      function($controller, $scope, cleaner) {
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
          cmp_amp: false,
          browser_update: false,
          items_per_page: '',
          items_in_blog: '',
          elements_in_rss: ''
        };

        $scope.routes = {
          saveConfig: 'api_v1_backend_settings_appearance_save',
          getConfig:  'api_v1_backend_settings_appearance_list'
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
         * @function pre
         * @memberOf SettingsCtrl
         *
         * @description
         *   Executes actions to adapt data from webservice to the template.
         */
        $scope.pre = function() {
          $scope.backup = {
            logo_favico:          $scope.settings.logo_favico,
            logo_simple:          $scope.settings.logo_simple,
            site_color:           $scope.settings.site_color,
            site_color_secondary: $scope.settings.site_color_secondary,
            logo_default:         $scope.settings.logo_default,
            logo_embed:           $scope.settings.logo_embed
          };
        };
        $scope.post = function() {
          var data = {
            instance: angular.copy($scope.instance),
            settings: angular.copy($scope.settings)
          };

          data = cleaner.clean(data, true);
          return data;
        };
      }
    ]);
})();
