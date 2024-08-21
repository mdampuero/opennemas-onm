(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  PressClippingConfigCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     * @requires messenger
     *
     * @description
     *   Provides actions to list notifications.
     */
    .controller('PressClippingConfigCtrl', [
      '$controller', '$scope', 'http', 'messenger', 'routing',
      function($controller, $scope, http, messenger, routing) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @memberof PressClippingConfigCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          getConfig: 'api_v1_backend_pressclipping_get_config',
          checkServer: 'api_v1_backend_pressclipping_check_server',
          saveConfig: 'api_v1_backend_pressclipping_save_config',
          uploadDump: 'api_v1_backend_pressclipping_upload_data',
          removeData: 'api_v1_backend_pressclipping_remove_data'
        };

        // Initialize settings with pressclipping_service
        $scope.settings = {
          pressclipping_service: {}
        };

        /**
         * @function init
         * @memberOf PressClippingConfigCtrl
         *
         * @description
         *   Initializes the form.
         */
        $scope.init = function() {
          http.get($scope.routes.getConfig).then(function(response) {
            $scope.settings = response.data;
            $scope.settings.pressclipping_service.service = $scope.settings.pressclipping_service.service || 'cedro';
            $scope.disableFlags('http');
          }, function() {
            $scope.disableFlags('http');
          });
        };

        /**
         * @function save
         * @memberof PressClippingConfigCtrl
         *
         * @description
         *  Saves the configuration
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
         * @memberof PressClippingConfigCtrl
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

        /**
         * @function check
         * @memberof PressClippingConfigCtrl
         *
         * @description
         * This function sends a POST request to remove the PressClipping settings.
         * It manages the HTTP request state by toggling flags and posting messages using
         * the `messenger` service. Upon completion or failure, it redirects the user
         * to the PressClipping settings page.
         *
         */
        $scope.removeSettings = function() {
          // Check if the HTTP checking flag is not set
          if (!$scope.flags.http.checking) {
            // Set the saving flag to true to indicate the request is being processed
            $scope.flags.http.saving = true;
          }

          // Send a POST request to the server to remove the settings
          return http.post($scope.routes.removeData)
            .then(function(response) {
              // Handle successful response
              if (!$scope.flags.http.checking) {
                // Disable HTTP flags after the request is done
                $scope.disableFlags('http');
                // Post the response data using the messenger service
                messenger.post(response.data);
              }

              // Redirect to the PressClipping settings page
              window.location = routing.generate('backend_pressclipping_settings');
            }, function(response) {
              // Handle error response
              if (!$scope.flags.http.checking) {
                // Disable HTTP flags after the request is done
                $scope.disableFlags('http');
                // Post the response data using the messenger service
                messenger.post(response.data);
              }

              // Redirect to the PressClipping settings page
              window.location = routing.generate('backend_pressclipping_settings');
            });
        };
      }
    ]);
})();
