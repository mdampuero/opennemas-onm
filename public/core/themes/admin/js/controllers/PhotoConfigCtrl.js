(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  PhotoConfigCtrl
     *
     * @requires $controller
     * @requires $scope
     *
     * @description
     *   Provides actions to edit, save and update articles.
     */
    .controller('PhotoConfigCtrl', [
      '$controller', '$scope', 'http', 'messenger',
      function($controller, $scope, http, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf PhotoConfigCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          list:         'backend_photos_list',
          getConfig:    'api_v1_backend_photo_get_config',
          saveConfig:   'api_v1_backend_photo_save_config',
        };

        /**
         * @memberOf PhotoConfigCtrl
         *
         * @description
         *  The extraFields object.
         *
         * @type {Object}
         */
        $scope.config = {};
        $scope.extra  = {};
        $scope.saving = false;

        /**
         * @function init
         * @memberOf PhotoConfigCtrl
         *
         * @description
         *   Initializes the form.
         */
        $scope.init = function() {
          $scope.list();
        };

        /**
         * @function list
         * @memberOf PhotoConfigCtrl
         *
         * @description
         *   Reloads the tag configuration.
         */
        $scope.list = function() {
          $scope.flags.http.loading = true;

          http.get($scope.routes.getConfig).then(function(response) {
            $scope.config = response.data.config;
            $scope.extra  = response.data.extra;
            $scope.disableFlags('http');
          }, function() {
            $scope.disableFlags('http');
          });
        };

        /**
         * @function save
         * @memberOf PhotoConfigCtrl
         *
         * @description
         *  Saves the configuration.
         */
        $scope.save = function() {
          $scope.flags.http.saving = true;

          http.put($scope.routes.saveConfig, { config: $scope.config, extra: $scope.extra })
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
