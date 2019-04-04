(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  OpinionListCtrl
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
    .controller('OpinionConfigCtrl', [
      '$controller', '$scope', 'cleaner', 'http', 'messenger',
      function($controller, $scope, cleaner, http, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @function init
         * @memberOf OpinionConfiCtrl
         *
         * @description
         *   Initializes the form.
         */
        $scope.init = function() {
          $scope.list();
        };

        /**
         * @function list
         * @memberOf OpinionConfigCtrl
         *
         * @description
         *   Reloads the tag configuration.
         */
        $scope.list = function() {
          $scope.flags.http.loading = true;

          http.get('api_v1_backend_opinion_config_show').then(function(response) {
            $scope.settings = response.data;
            $scope.disableFlags('http');
          }, function() {
            $scope.disableFlags('http');
          });
        };

        /**
         * @function save
         * @memberOf OpinionConfigCtrl
         *
         * @description
         *   Saves the configuration.
         */
        $scope.save = function() {
          $scope.flags.http.saving = true;

          http.put('api_v1_backend_opinion_config_save', $scope.settings)
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
