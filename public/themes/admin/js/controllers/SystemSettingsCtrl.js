(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  SystemSettingsCtrl
     *
     * @requires $controller
     * @requires $rootScope
     * @requires $scope
     *
     * @description
     *   Handles actions for paywall settings configuration form.
     */
    .controller('SystemSettingsCtrl', ['$controller', '$rootScope', '$scope', 'http',
      function($controller, $rootScope, $scope, http) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        $scope.locale = {
          backend: 'en',
          frontend: [],
          main: 0,
        };

        /**
         * @function init
         * @memberOf SystemSettingsCtrl
         *
         * @description
         *   Initialize list of other ga account codes.
         *
         * @param Object gaCodes The list of other ga account codes.
         */
        $scope.init = function(gaCodes) {
          if (angular.isArray(gaCodes)) {
            $scope.gaCodes = gaCodes;
          } else {
            $scope.gaCodes = [];
          }
        };

        /**
         * @function addInput
         * @memberOf SystemSettingsCtrl
         *
         * @description
         *   Add new input for ga tracking code.
         *
         * @param integer index The index of the domain to remove.
         */
        $scope.addGanalytics = function() {
          $scope.gaCodes.push({
            apiKey:'',
            baseDomain:'',
            customVar:''
          });
        };

        $scope.addLocale = function(item) {
          if ($scope.locale.frontend.length === 0) {
            $scope.locale.main = 1;
          }

          $scope.locale.frontend.push(item);
        };

        /**
         * @function getLocales
         * @memberOf SystemSettingsCtrl
         *
         * @description
         *   Returns a list of locales by name.
         *
         * @param {String} query The string to search by.
         *
         * @return {Array} The list of locales.
         */
        $scope.getLocales = function(query) {
          var route = {
              name: 'api_v1_backend_settings_locale_list',
              params: { q: query }
          };

          $scope.searching = true;

          return http.get(route).then(function(response) {
            $scope.searching = false;
            return response.data;
          });
        };

        /**
         * @function removeInput
         * @memberOf SystemSettingsCtrl
         *
         * @description
         *   Removes a ga tracking code input.
         *
         * @param integer index The index of the input to remove.
         */
        $scope.removeGanalytics = function(gaCodes, index) {
          $scope.gaCodes.splice(index, 1);
        };
      }
    ]);
})();
