(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ExternalSettingsCtrl
     *
     * @requires $controller
     * @requires $scope
     */
    .controller('ExternalSettingsCtrl', [
      '$controller', '$scope',
      function($controller, $scope) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('SettingsCtrl', { $scope: $scope }));

        /**
         * The settings object.
         *
         * @type {Object}
         */
        $scope.settings = {
          google_analytics: [ { api_key: '' } ],
          data_layer: [],
          gfk: {}
        };

        $scope.routes = {
          saveConfig: 'api_v1_backend_settings_external_save',
          getConfig: 'api_v1_backend_settings_external_list'
        };

        /**
         * @function addGanalytics
         * @memberOf ExternalSettingsCtrl
         *
         * @description
         *   Add new input for ga tracking code.
         */
        $scope.addGanalytics = function(master) {
          $scope.settings.google_analytics
            .push({ api_key: '', master: master || false });
        };

        /**
         * @function addDatalayerVariable
         * @memberOf ExternalSettingsCtrl
         *
         * @description
         *   Add new pair key:value to Datalayer.
         */
        $scope.addDatalayerVariable = function() {
          $scope.settings.data_layer.push({ key: null, value: null });
        };

        /**
         * @function removeGanalytics
         * @memberOf ExternalSettingsCtrl
         *
         * @description
         *   Removes a ga tracking code input.
         *
         * @param {Integer} index The index of the input to remove.
         */
        $scope.removeGanalytics = function(index) {
          $scope.settings.google_analytics.splice(index, 1);
        };

        /**
         * @function removeDatalayerVariable
         * @memberOf ExternalSettingsCtrl
         *
         * @description
         *   Removes a data layer variable input.
         *
         * @param {Integer} index The index of the input to remove.
         */
        $scope.removeDatalayerVariable = function(index) {
          $scope.settings.data_layer.splice(index, 1);
        };
      }
    ]);
})();
