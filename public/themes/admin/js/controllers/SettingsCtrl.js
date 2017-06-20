(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  SettingsCtrl
     *
     * @requires $controller
     * @requires $rootScope
     * @requires $scope
     *
     * @description
     *   Handles actions for paywall settings configuration form.
     */
    .controller('SettingsCtrl', ['$controller', '$rootScope', '$scope', 'http', 'messenger',
      function($controller, $rootScope, $scope, http, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        $scope.overlay = {};
        $scope.locale = {
          backend:   'en',
          frontend:  [],
          time_zone: 'UTC'
        };

        $scope.enabled = {};

        /**
         * @function init
         * @memberOf SettingsCtrl
         *
         * @description
         *   Initialize list of other ga account codes.
         *
         * @param Object gaCodes The list of other ga account codes.
         */
        $scope.init = function(gaCodes) {
          $scope.gaCodes = [];

          if (angular.isArray(gaCodes)) {
            $scope.gaCodes = gaCodes;
          }
        };

        /**
         * @function addInput
         * @memberOf SettingsCtrl
         *
         * @description
         *   Add new input for ga tracking code.
         */
        $scope.addGanalytics = function() {
          $scope.gaCodes.push({ apiKey: '', baseDomain: '', customVar: '' });
        };

        /**
         * @function addLocale
         * @memberOf SettingsCtrl
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
         * @memberOf SettingsCtrl
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
         * @function list
         * @memberOf SettingsCtrl
         *
         * @description
         *   Lists all settings.
         */
        $scope.list = function() {
          $scope.loading = true;

          http.get('api_v1_backend_settings_list').then(function(response) {
            $scope.settings = response.data.settings;
            $scope.country  = response.data.country;
            $scope.extra    = response.data.extra;

            $scope.backup = {
              site_color:           $scope.settings.site_color,
              site_color_secondary: $scope.settings.site_color_secondary
            };

            $scope.settings.site_logo = '/media/opennemas/sections/' + $scope.settings.site_logo;
            $scope.settings.mobile_logo = '/media/opennemas/sections/' + $scope.settings.mobile_logo;
            $scope.settings.favico = '/media/opennemas/sections/' + $scope.settings.favico;

            $scope.loading = false;
          }, function() {
            $scope.loading = false;
          });
        };

        /**
         * @function removeInput
         * @memberOf SettingsCtrl
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
         * @memberOf SettingsCtrl
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

        /**
         * @function save
         * @memberOf SettingsCtrl
         *
         * @description
         *   Saves settings.
         */
        $scope.save = function() {
          $scope.saving = true;

          var data = { country: $scope.country, settings: $scope.settings };

          http.put('api_v1_backend_settings_save', data)
            .then(function(response) {
              $scope.saving = false;
              messenger.post(response.data);
            }, function(response) {
              $scope.saving = false;
              messenger.post(response.data);
            });
        }
      }
    ]);
})();
