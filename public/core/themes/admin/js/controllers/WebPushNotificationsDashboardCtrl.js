(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ArticleListCtrl
     *
     * @requires $controller
     * @requires $location
     * @requires $scope
     * @requires $timeout
     * @requires http
     * @requires messenger
     * @requires linker
     * @requires localizer
     * @requires oqlEncoder
     *
     * @description
     *   Provides actions to list articles.
     */
    .controller('WebPushNotificationsDashboardCtrl', [
      '$controller', '$scope', 'cleaner', 'http', 'messenger',
      function($controller, $scope, cleaner, http, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf WebPushNotificationsDashboardCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          checkServer:  'api_v1_backend_webpush_notifications_check_server'
        };

        $scope.settings = {
          webpush_restricted_hours: []
        };

        /**
         * @function init
         * @memberOf WebPushNotificationsDashboardCtrl
         *
         * @description
         *   Initializes the form.
         */
        $scope.init = function() {
          http.get('api_v1_backend_webpush_notifications_get_config').then(function(response) {
            $scope.settings = response.data;
            $scope.settings.webpush_delay = $scope.settings.webpush_delay || '1';
            $scope.disableFlags('http');
          }, function() {
            $scope.disableFlags('http');
          });
        };
      }
    ]);
})();
