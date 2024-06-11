(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  OpenAIConfigCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     * @requires messenger
     *
     * @description
     *   Provides actions to list notifications.
     */
    .controller('OpenAIConfigCtrl', [
      '$controller', '$scope', 'http', 'messenger', 'routing',
      function($controller, $scope, http, messenger, routing) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf OpenAIConfigCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          checkServer: 'api_v1_backend_openai_check_server',
          getConfig:   'api_v1_backend_openai_get_config',
          saveConfig:  'api_v1_backend_openai_save_config',
        };

        $scope.settings = {
          openai_service:     'custom',
          openai_credentials: [],
          openai_config:      [],
        };

        /**
         * @function init
         * @memberOf OpenAIConfigCtrl
         *
         * @description
         *   Initializes the form.
         */
        $scope.init = function() {
          http.get($scope.routes.getConfig).then(function(response) {
            $scope.settings = response.data;
            $scope.disableFlags('http');
          }, function() {
            $scope.disableFlags('http');
          });
        };

        /**
         * @function save
         * @memberOf OpenAIConfigCtrl
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

        /**
         * @function check
         * @memberOf OpenAIConfigCtrl
         *
         * @description
         *   Checks the connection to the server.
         */
        $scope.check = function() {
          $scope.flags.http.checking = true;

          $scope.save()
            .then(function() {
              var route = {
                name: $scope.routes.checkServer
              };

              http.get(route).then(function() {
                $scope.disableFlags('http');
                $scope.status = 'success';
              }, function() {
                $scope.disableFlags('http');
                $scope.status = 'failure';
              });
            }, function() {
              $scope.disableFlags('http');
            });
        };
      }
    ]);
})();
