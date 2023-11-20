(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ThemeSettingsCtrl
     *
     * @requires $controller
     * @requires $scope
     */
    .controller('ThemeSettingsCtrl', [
      '$controller', '$scope',
      function($controller, $scope) {
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
          gfk: {},
          logo_enabled: false,
          site_color: '',
          site_color_secondary: '',
          theme_skin: 'default'
        };

        $scope.routes = {
          saveConfig: 'api_v1_backend_settings_theme_save',
          getConfig: 'api_v1_backend_settings_theme_list'
        };
      }
    ]);
})();
