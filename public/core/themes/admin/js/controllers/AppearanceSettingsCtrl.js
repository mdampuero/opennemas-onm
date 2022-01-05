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
      '$controller', '$scope', 'cleaner', 'http',
      function($controller, $scope, cleaner, http) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('SettingsCtrl', { $scope: $scope }));
        $scope.settings = {
          site_color: '',
          site_color_secondary: '',
          logo_enabled: false,
          logo_defaultID: '',
          logo_simpleID: '',
          logo_favicoID: '',
          logo_embedID: '',
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

        $scope.list = function() {
          $scope.loading = true;
          const params = $scope.settings;

          http.get({
            name: 'api_v1_backend_settings_list',
            params: { params }
          }).then(function(response) {
            $scope.instance = response.data.instance;
            $scope.extra    = response.data.extra;
            $scope.settings = angular.merge($scope.settings, response.data.settings);

            $scope.pre();

            $scope.loading = false;
          }, function() {
            $scope.loading = false;
          });
        };

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
          if ($scope.settings.logo_defaultID) {
            data.settings.logo_default = parseInt($scope.settings.logo_defaultID.pk_content);
            delete data.settings.logo_defaultID;
          }

          if ($scope.settings.logo_simpleID) {
            data.settings.logo_simple = parseInt($scope.settings.logo_simpleID.pk_content);
            delete data.settings.logo_simpleID;
          }

          if (data.settings.logo_favicoID) {
            data.settings.logo_favico = parseInt($scope.settings.logo_favicoID.pk_content);
            delete data.settings.logo_favicoID;
          }

          if (data.settings.logo_embedID) {
            data.settings.logo_embed = parseInt($scope.settings.logo_embedID.pk_content);
            delete data.settings.logo_embedID;
          }
          return data;
        };
      }
    ]);
})();
