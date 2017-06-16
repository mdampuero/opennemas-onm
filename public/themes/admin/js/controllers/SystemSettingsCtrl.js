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
          main: null,
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

        /**
         * @function addLocale
         * @memberOf SystemSettingsCtrl
         *
         * @description
         *   Add a new locale to the list of frontend locales.
         *
         * @param Object The locale to add.
         */
        $scope.addLocale = function(item) {
          if ($scope.locale.frontend.length === 0) {
            $scope.locale.main = item.code;
          }

          var codes = $scope.locale.frontend.map(function (e) {
            return e.code;
          });

          if (codes.indexOf(item.code) === -1) {
            $scope.locale.frontend.push(item);
          }
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

        /**
         * @function removeLocale
         * @memberOf SystemSettingsCtrl
         *
         * @description
         *   Remove a locale from the list of frontend locales.
         *
         * @param integer index The index of the locale to remove in the list of
         *                      locales.
         */
        $scope.removeLocale = function(index) {
          var item = $scope.locale.frontend[index];

          $scope.locale.frontend.splice(index, 1);

          if ($scope.locale.frontend.length === 0) {
            $scope.locale.main = null;

            return;
          }

          if (item.code !== $scope.locale.main) {
            return;
          }

          if (index >= $scope.locale.frontend.length) {
            index = $scope.locale.frontend.length - 1;
          }

          $scope.locale.main = $scope.locale.frontend[index].code;
        };
      }
    ]);
})();
