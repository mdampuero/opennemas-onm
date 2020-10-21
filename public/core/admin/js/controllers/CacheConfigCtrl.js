(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  CacheConfigCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     *
     * @description
     *   description
     */
    .controller('CacheConfigCtrl', [
      '$controller', '$scope', 'cleaner', 'http', 'messenger',
      function($controller, $scope, cleaner, http, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @memberOf CacheConfigCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          updateConfig: 'api_v1_backend_cache_update_config',
          getConfig:    'api_v1_backend_cache_get_config'
        };

        /**
         * @function getConfig
         * @memberOf CacheConfigCtrl
         *
         * @description
         *   Gets the service configuration.
         */
        $scope.getConfig = function() {
          var route = {
            name: $scope.routes.getConfig,
            params: { service: 'smarty' }
          };

          http.get(route).then(function(response) {
            $scope.disableFlags('http');
            $scope.items = response.data.items;
          }, function(response) {
            $scope.disableFlags('http');
            messenger.post(response.data);
          });
        };

        /**
         * @inheritdoc
         */
        $scope.isSelectable = function() {
          return false;
        };

        /**
         * @function updateConfig
         * @memberOf CacheConfigCtrl
         *
         * @description
         *   Updates the service configuration.
         */
        $scope.updateConfig = function() {
          $scope.flags.http.saving = true;

          var route = {
            name: $scope.routes.getConfig,
            params: { service: 'smarty' }
          };

          http.put(route, cleaner.clean($scope.items)).then(function(response) {
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
