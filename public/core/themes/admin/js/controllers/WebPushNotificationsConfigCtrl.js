(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  WebPushNotificationsConfigCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     * @requires messenger
     *
     * @description
     *   Provides actions to list notifications.
     */
    .controller('WebPushNotificationsConfigCtrl', [
      '$controller', '$scope', 'http', 'messenger', 'routing',
      function($controller, $scope, http, messenger, routing) {
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
          checkServer: 'api_v1_backend_webpush_notifications_check_server',
          getConfig:   'api_v1_backend_webpush_notifications_get_config',
          saveConfig:  'api_v1_backend_webpush_notifications_save_config',
          removeData:  'api_v1_backend_webpush_notifications_remove_data'
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
          http.get($scope.routes.getConfig).then(function(response) {
            $scope.settings = response.data;
            $scope.settings.webpush_delay = $scope.settings.webpush_delay || '1';
            $scope.settings.webpush_service.service = $scope.settings.webpush_service.service || 'webpushr';
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

        $scope.removeSavedSettings = function() {
          if (!$scope.flags.http.checking) {
            $scope.flags.http.saving = true;
          }

          return http.post($scope.routes.removeData)
            .then(function(response) {
              if (!$scope.flags.http.checking) {
                $scope.disableFlags('http');
                messenger.post(response.data);
              }
              window.location = routing.generate('backend_webpush_notifications_config');
            }, function(response) {
              if (!$scope.flags.http.checking) {
                $scope.disableFlags('http');
                messenger.post(response.data);
              }
              window.location = routing.generate('backend_webpush_notifications_config');
            });
        };

        $scope.options = [
          { value: '1', label: '1 min' },
          { value: '5', label: '5 mins' },
          { value: '10', label: '10 mins' },
          { value: '15', label: '15 mins' },
          { value: '30', label: '30 mins' },
          { value: '60', label: '60 mins' }
        ];
      }
    ]);
})();
