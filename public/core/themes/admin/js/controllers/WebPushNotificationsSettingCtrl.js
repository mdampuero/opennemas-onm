(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  NewsletterSettingsCtrl
     *
     * @requires $controller
     * @requires $uibModal
     * @requires $scope
     *
     * @description
     *   Handles actions for advertisement inner.
     */
    .controller('NewsletterSettingsCtrl', [
      '$scope', 'cleaner', 'http', 'messenger',
      function($scope, cleaner, http, messenger) {
        /**
         * @memberOf NewsletterSettingsCtrl
         *
         * @description
         *  The settings object.
         *
         * @type {Object}
         */
        $scope.settings = {
          newsletter_handler: null,
          'actOn.marketingLists': []
        };

        /**
         * @function addList
         * @memberOf NewsletterSettingsCtrl
         *
         * @description
         *   Adds a new act-On markelint list.
         */
        $scope.addList = function() {
          if (!angular.isArray($scope.settings['actOn.marketingLists'])) {
            $scope.settings['actOn.marketingLists'] = [];
          }

          $scope.settings['actOn.marketingLists'].push({ name: '', id: '' });
        };

        /**
         * @function list
         * @memberOf NewsletterSettingsCtrl
         *
         * @description
         *   Gets the list of settings.
         */
        $scope.list = function() {
          $scope.loading = true;

          http.get('api_v1_backend_newsletters_settings_list')
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
         * @memberOf NewsletterSettingsCtrl
         *
         * @description
         *   Removes a field from the field list.
         *
         * @param {Integer} index The index of the field to remove.
         */
        $scope.removeList = function(index) {
          $scope.settings['actOn.marketingLists'].splice(index, 1);
        };

        /**
         * @function save
         * @memberOf NewsletterSettingsCtrl
         *
         * @description
         *   Saves the list of settings.
         */
        $scope.save = function() {
          $scope.saving = true;

          var data = cleaner.clean($scope.settings);

          http.put('api_v1_backend_newsletters_settings_save', data)
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
