(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SubscriberSettingsCtrl
     *
     * @requires $controller
     * @requires $uibModal
     * @requires $scope
     *
     * @description
     *   Handles actions for advertisement inner.
     */
    .controller('SubscriberSettingsCtrl', [
      '$scope', 'cleaner', 'http', 'messenger',
      function($scope, cleaner, http, messenger) {
        /**
         * @memberOf SubscriberSettingsCtrl
         *
         * @description
         *  The settings object.
         *
         * @type {Object}
         */
        $scope.settings = { fields: [] };

        /**
         * @function add
         * @memberOf SubscriberSettingsCtrl
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
         * @memberOf SubscriberSettingsCtrl
         *
         * @description
         *   Gets the list of settings.
         */
        $scope.list = function() {
          $scope.loading = true;

          http.get('api_v1_backend_subscribers_settings_list')
            .then(function(response) {
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
         * @memberOf SubscriberSettingsCtrl
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
         * @memberOf SubscriberSettingsCtrl
         *
         * @description
         *   Saves the list of settings.
         */
        $scope.save = function() {
          $scope.saving = true;

          var data = cleaner.clean($scope.settings);

          http.put('api_v1_backend_subscribers_settings_save', data)
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
