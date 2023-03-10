(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  InternalSettingsCtrl
     *
     * @requires $controller
     * @requires $scope
     */
    .controller('InternalSettingsCtrl', [
      '$controller', '$scope',
      function($controller, $scope) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('SettingsCtrl', { $scope: $scope }));
        $scope.settings = {};
        $scope.routes = {
          saveConfig: 'api_v1_backend_settings_internal_save',
          getConfig: 'api_v1_backend_settings_internal_list'
        };
      }
    ]);
})();
