(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ArticleListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     * @requires messenger
     *
     * @description
     *   Provides actions to edit, save and update configs.
     */
    .controller('EventConfigCtrl', [
      '$controller', '$scope', 'http', 'messenger',
      function($controller, $scope, http, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        $scope.saving = false;

        /**
         * @function init
         * @memberOf EventConfigCtrl
         *
         * @description
         *   Initializes the form.
         */
        $scope.init = function() {
          $scope.init();
        };

        /**
         * @function init
         * @memberOf EventConfigCtrl
         *
         * @description
         *   Reloads the event configuration.
         */
        $scope.init = function() {
          $scope.flags.http.loading = true;

          http.get('api_v1_backend_event_get_config').then(function(response) {
            $scope.config = response.data.config;
            $scope.disableFlags('http');
          }, function() {
            $scope.disableFlags('http');
          });
        };

        /**
         * @function save
         * @memberOf EventConfigCtrl
         *
         * @description
         *   Saves the configuration.
         */
        $scope.save = function() {
          $scope.flags.http.saving = true;
          http.put('api_v1_backend_event_save_config', { config: $scope.config })
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
