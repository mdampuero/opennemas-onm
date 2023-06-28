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
    '$controller', '$scope', 'cleaner', 'http', 'messenger',
    function($controller, $scope, cleaner, http, messenger) {
      // Initialize the super class and extend it.
      $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

      /**
       * @memberOf UserSettingsCtrl
       *
       * @description
       *  The extraFields object.
       *
       * @type {Object}
       */
      $scope.extraFields = {};
      $scope.saving = false;

      $scope.init = function(extraFields) {
        if (extraFields !== null) {
          $scope.extraFields = extraFields;
        }
      };

      /**
       * @function init
       * @memberOf AlbumConfigCtrl
       *
       * @description
       *   Initializes the form.
       */
      $scope.initList = function() {
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

        var data = { extraFields: JSON.stringify(cleaner.clean($scope.extraFields)) };
        var combinedData = Object.assign({}, $scope.settings, data);

        http.put('api_v1_backend_album_save_config', combinedData)
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
