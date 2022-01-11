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
      '$controller', '$scope',
      function($controller, $scope) {
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
        $scope.routes = {
          saveConfig: 'api_v1_backend_settings_general_save',
          getConfig: 'api_v1_backend_settings_general_list'
        };
      }
    ]);
})();
