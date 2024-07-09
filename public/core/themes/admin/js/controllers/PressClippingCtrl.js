(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  PressClippingCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     * @requires messenger
     *
     * @description
     *   Provides actions to list notifications.
     */
    .controller('PressClippingCtrl', [
      '$controller', '$scope', 'http', 'messenger', 'routing',
      function($controller, $scope, http, messenger, routing) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @memberOf PressClippingCtrl
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
        };

        // Initialize settings with pressclipping_service
        $scope.settings = {
          pressclipping_service: {}
        };

        /**
         * @function init
         * @memberOf PressClippingCtrl
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
         * @memberof PressClippingCtrl
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
         * @memberOf PressClippingCtrl
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
