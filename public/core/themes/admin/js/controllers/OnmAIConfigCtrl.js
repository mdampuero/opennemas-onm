(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  OnmAIConfigCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     * @requires messenger
     *
     * @description
     *   Provides actions to list notifications.
     */
    .controller('OnmAIConfigCtrl', [
      '$controller', '$scope', 'http', 'messenger', '$uibModal',
      function($controller, $scope, http, messenger, $uibModal) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf OnmAIConfigCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          getConfig: 'api_v1_backend_onmai_get_config',
          saveConfig: 'api_v1_backend_onmai_save_config',
          uploadConfig: 'api_v1_backend_onmai_upload_config',
          downloadConfig: 'api_v1_backend_onmai_download_config',
        };

        $scope.models = [];

        /**
         * @function init
         * @memberOf OnmAIConfigCtrl
         *
         * @description
         *   Initializes the form.
         */
        $scope.init = function() {
          http.get($scope.routes.getConfig).then(function(response) {
            $scope.settings = response.data;
            $scope.filterModels();
            $scope.disableFlags('http');
          }, function() {
            $scope.disableFlags('http');
          });
        };

        /**
         * @function save
         * @memberOf OnmAIConfigCtrl
         *
         * @description
         *   Saves the configuration.
         */
        $scope.save = function() {
          if (!$scope.flags.http.checking) {
            $scope.flags.http.saving = true;
          }

          var data = $scope.settings;

          return http.put($scope.routes.saveConfig, data)
            .then(function(response) {
              if (!$scope.flags.http.checking) {
                $scope.disableFlags('http');
                messenger.post(response.data);
              }
            }, function(response) {
              if (!$scope.flags.http.checking) {
                $scope.disableFlags('http');
                messenger.post(response.data);
              }
            });
        };

        $scope.filterModels = function() {
          $scope.models = $scope.settings.models.filter(function(model) {
            return model.id.indexOf($scope.settings.onmai_config.service) === 0;
          });
          if ($scope.models.length > 0) {
            $scope.settings.onmai_config.model = $scope.models[0].id;
          } else if ($scope.settings.onmai_config.service === 'onmai') {
            $scope.settings.onmai_config.model = '';
          }
        };
      }
    ]);
})();
