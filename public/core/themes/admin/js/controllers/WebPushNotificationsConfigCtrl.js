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
         *  The extraFields object.
         *
         * @type {Object}
         */
        $scope.extraFields = {};

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
      }
    ]);
})();
