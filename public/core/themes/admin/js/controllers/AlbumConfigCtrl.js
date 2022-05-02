(function() {
  'use strict';

  /**
   * @ngdoc controller
   * @name  AlbumListCtrl
   *
   * @requires $controller
   * @requires $scope
   * @requires http
   * @requires messenger
   *
   * @description
   *   Provides actions to list articles.
   */
  angular.module('BackendApp.controllers').controller('AlbumConfigCtrl', [
    '$controller', '$scope', 'http', 'messenger',
    function($controller, $scope, http, messenger) {
      // Initialize the super class and extend it.
      $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

      /**
       * @function init
       * @memberOf AlbumConfigCtrl
       *
       * @description
       *   Initializes the form.
       */
      $scope.init = function() {
        $scope.list();
      };

      /**
       * @function list
       * @memberOf AlbumConfigCtrl
       *
       * @description
       *   Reloads the configuration.
       */
      $scope.list = function() {
        $scope.flags.http.loading = true;

        http.get('api_v1_backend_album_get_config').then(function(response) {
          $scope.settings = response.data;
          $scope.disableFlags('http');
        }, function() {
          $scope.disableFlags('http');
        });
      };

      /**
       * @function save
       * @memberOf AlbumConfigCtrl
       *
       * @description
       *   Saves the configuration.
       */
      $scope.save = function() {
        $scope.flags.http.saving = true;

        http.put('api_v1_backend_album_save_config', $scope.settings)
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
