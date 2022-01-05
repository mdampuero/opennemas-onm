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
    .controller('GeneralSettingsCtrl', [
      '$controller', '$scope', 'cleaner', 'http', 'messenger', 'oqlEncoder',
      function($controller, $scope, cleaner, http, messenger, oqlEncoder) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('SettingsCtrl', { $scope: $scope }));
        $scope.settings = {
          site_name: '',
          site_footer: '',
          site_title: '',
          site_keywords: '',
          site_description: '',
          refresh_interval: 900,
          webmastertools_google: '',
          webmastertools_bing: ''
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
          $scope.settings = $scope.settings;
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
