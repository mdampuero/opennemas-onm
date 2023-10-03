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
    .controller('WebPushNotificationsConfigCtrl', [
      '$controller', '$scope', 'cleaner', 'http', 'messenger',
      function($controller, $scope, cleaner, http, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf WebPushNotificationsConfigCtrl
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
         * @memberOf WebPushNotificationsConfigCtrl
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

        /**
         * @function save
         * @memberOf WebPushNotificationsConfigCtrl
         *
         * @description
         *   Saves the configuration.
         */
        $scope.save = function() {
          $scope.flags.http.saving = true;

          var data = $scope.settings;

          http.put('api_v1_backend_webpush_notifications_save_config', data)
            .then(function(response) {
              $scope.disableFlags('http');
              messenger.post(response.data);
            }, function(response) {
              $scope.disableFlags('http');
              messenger.post(response.data);
            });
        };

        /**
         * @function loadHours
         * @memberOf NewsletterTemplateCtrl
         *
         * @description
         *   Returns the filtered list of hours given a search query.
         *
         * @param {String} $query The text to filter the hours.
         */
        $scope.loadHours = function($query) {
          return $scope.settings.hours.filter(function(el) {
            return el.indexOf($query) >= 0;
          });
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

          http.get(route).then(function(response) {
            $scope.disableFlags('http');
            $scope.status = 'success';
          }, function() {
            $scope.disableFlags('http');
            $scope.status = 'failure';
          });
        };

        $scope.options = [
          { value: '1', label: '1 min' },
          { value: '5', label: '5 mins' },
          { value: '10', label: '10 mins' },
          { value: '30', label: '30 mins' },
          { value: '60', label: '60 mins' }
        ];
      }
    ]);
})();
