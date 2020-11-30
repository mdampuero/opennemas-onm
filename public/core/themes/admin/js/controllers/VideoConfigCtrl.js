(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  videoListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     * @requires messenger
     *
     * @description
     *   Provides actions to list articles.
     */
    .controller('VideoConfigCtrl', [
      '$controller', '$scope', 'http', 'messenger',
      function($controller, $scope, http, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @function init
         * @memberOf VideoConfigCtrl
         *
         * @description
         *   Initializes the form.
         */
        $scope.init = function() {
          $scope.list();
        };

        /**
         * @function list
         * @memberOf VideoConfigCtrl
         *
         * @description
         *   Reloads the tag configuration.
         */
        $scope.list = function() {
          $scope.flags.http.loading = true;

          http.get('api_v1_backend_video_config_show').then(function(response) {
            $scope.settings = response.data;

            $scope.settings.total_front_more = parseInt($scope.settings.total_front_more);

            $scope.disableFlags('http');
          }, function() {
            $scope.disableFlags('http');
          });
        };

        /**
         * @function save
         * @memberOf VideoConfigCtrl
         *
         * @description
         *   Saves the configuration.
         */
        $scope.save = function() {
          $scope.flags.http.saving = true;

          http.put('api_v1_backend_video_config_save', $scope.settings)
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
