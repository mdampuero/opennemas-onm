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
      function($controller, $scope, http) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf PressClippingCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          checkServer: 'api_v1_backend_presclipping_check_server',
        };

        // Initialize settings with pressclipping_service
        $scope.settings = {
          pressclipping_service: {}
        };

        /**
         * @function init
         * @memberOf WebPushNotificationsConfigCtrl
         *
         * @description
         *   Initializes the form.
         */
        $scope.init = function() {
          $scope.settings.pressclipping_service.service = 'cedro';

          $scope.disableFlags('http');
        };

        /**
         * @function check
         * @memberOf WebPushNotificationsConfigCtrl
         *
         * @description
         *   Checks the connection to the server.
         */
        $scope.check = function() {
          $scope.flags.http.checking = true;

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
        };
      }
    ]);
})();
