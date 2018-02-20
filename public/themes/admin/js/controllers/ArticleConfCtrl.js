(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ArticleListCtrl
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
    .controller('ArticleConfCtrl', [
      '$scope', 'cleaner', 'http', 'messenger',
      function($scope, cleaner, http, messenger) {
        /**
         * @memberOf UserSettingsCtrl
         *
         * @description
         *  The settings object.
         *
         * @type {Object}
         */
        $scope.settings = {};

        /**
         * @function add
         * @memberOf UserSettingsCtrl
         *
         * @description
         *   Adds a new field to the field list.
         */
        $scope.addGroup = function() {
          $scope.settings.push({
            group: '',
            title: '',
            fields: {
              name: '',
              type: '',
              key: ''
            }
          });
        };

        /**
         * Updates an item.
         *
         * @param int    index   Index of the item to update in contents.
         * @param int    id      Id of the item to update.
         * @param string route   Route name.
         * @param string name    Name of the property to update.
         * @param mixed  value   New value.
         * @param string loading Name of the property used to show work-in-progress.
         */
        $scope.saveConf = function(index, id, route, name, value, loading, reload) {
          $scope.saving = true;

          var data = cleaner.clean($scope.settings);

          http.put('backend_ws_users_settings_save', data)
            .then(function(response) {
              $scope.saving = false;

              messenger.post(response.data);
            }, function(response) {
              $scope.saving = false;

              messenger.post(response.data);
            });
          $scope.list();
        };
      }
    ]);
})();
