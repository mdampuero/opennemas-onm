(function () {
  'use strict';

  /**
   * @ngdoc controller
   * @name  UserSettingsCtrl
   *
   * @requires $controller
   * @requires $uibModal
   * @requires $scope
   *
   * @description
   *   Handles actions for advertisement inner.
   */
  angular.module('BackendApp.controllers').controller('UserSettingsCtrl', [
    '$scope', 'Cleaner', 'http', 'messenger',
    function($scope, Cleaner, http, messenger) {
      /**
       * @memberOf UserSettingsCtrl
       *
       * @description
       *  The settings object.
       *
       * @type {Object}
       */
      $scope.settings = { fields: [] };

      /**
       * @function add
       * @memberOf UserSettingsCtrl
       *
       * @description
       *   Adds a new field to the field list.
       */
      $scope.addField = function() {
        if (!$scope.settings.fields) {
          $scope.settings.fields = [];
        }

        $scope.settings.fields.push({ name: '', title: '', type: 'text' });
      };

      /**
       * @function list
       * @memberOf UserSettingsCtrl
       *
       * @description
       *   Gets the list of settings.
       */
      $scope.list = function() {
        $scope.loading = true;

        http.get('backend_ws_users_settings_list').then(function(response) {
          $scope.loading = false;

          if (response.data.settings) {
            $scope.settings = response.data.settings;
          }
        }, function(response) {
          $scope.loading = false;

          messenger.post(response.data);
        });
      };

      /**
       * @function removeField
       * @memberOf UserSettingsCtrl
       *
       * @description
       *   Removes a field from the field list.
       *
       * @param {Integer} index The index of the field to remove.
       */
      $scope.removeField = function(index) {
        $scope.settings.fields.splice(index, 1);
      };

      /**
       * @function save
       * @memberOf UserSettingsCtrl
       *
       * @description
       *   Saves the list of settings.
       */
      $scope.save = function() {
        $scope.saving = true;

        var data = Cleaner.clean($scope.settings);

        http.put('backend_ws_users_settings_save', data)
          .then(function(response) {
            $scope.saving = false;

            messenger.post(response.data);
          }, function(response) {
            $scope.saving = false;

            messenger.post(response.data);
          });
      };

      $scope.list();
    }
  ]);
})();
