(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  SettingCtrl
     *
     * @requires $location
     * @requires $routeParams
     * @requires $scope
     * @requires http
     * @requires routing
     * @requires messenger
     *
     * @description
     *   Handles all actions in users listing.
     */
    .controller('SettingCtrl', [
      '$location', '$routeParams', '$scope', '$timeout', 'http', 'routing', 'messenger',
      function ($location, $routeParams, $scope, $timeout, http, routing, messenger) {
        /**
         * @function save
         * @memberOf SettingCtrl
         *
         * @description
         *   Saves a new user.
         */
        $scope.save = function() {
          $scope.saving = 1;

          http.post('manager_ws_settings_save', $scope.settings)
            .then(function (response) {
              $scope.saving = 0;
              messenger.post(response.data);
            }, function() {
              $scope.saving = 0;
            });
        };

        // Frees up memory on destroy event
        $scope.$on('$destroy', function() {
          $scope.settings = null;
          $scope.extra    = null;
        });

        $scope.loading = 1;

        http.get('manager_ws_settings_list').then(function (response) {
          $scope.settings = response.data.settings;
          $scope.extra    = response.data.extra;
          $scope.loading  = 0;
        }, function () {
          $scope.loading = 0;
        });
      }
  ]);
})();
