(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  MasterSettingsCtrl
     *
     * @requires $controller
     * @requires $scope
     */
    .controller('MasterSettingsCtrl', [
      '$controller', '$scope',
      function($controller, $scope) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('SettingsCtrl', { $scope: $scope }));

        /**
         * @memberOf MasterSettingsCtrl
         *
         * @description
         *  The settings object with default values.
         *
         * @type {Object}
         */
        $scope.settings = {
          theme_skin: 'default',
          gfk: {}
        };

        $scope.routes = {
          saveConfig: 'api_v1_backend_settings_master_save',
          getConfig: 'api_v1_backend_settings_master_list'
        };
      }
    ]);
})();
