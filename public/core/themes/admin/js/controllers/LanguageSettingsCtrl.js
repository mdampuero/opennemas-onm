(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  LanguageSettingsCtrl
     *
     * @requires $controller
     * @requires $scope
     */
    .controller('LanguageSettingsCtrl', [
      '$controller', '$scope', 'cleaner', 'http',
      function($controller, $scope, cleaner, http) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('SettingsCtrl', { $scope: $scope }));
        $scope.settings = {
          locale: {
            backend: {
              language: { selected: 'en_US' },
              timezone: 'UTC'
            },
            frontend: {
              language: {
                available: [],
                selected: null,
                slug: {}
              },
              timezone: 'UTC'
            }
          },
          translators: [],
        };
        $scope.routes = {
          saveConfig: 'api_v1_backend_settings_language_save',
          getConfig:  'api_v1_backend_settings_language_list',
          getLocales: 'api_v1_backend_settings_locale_list'
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
          if (!$scope.settings.locale.frontend.language) {
            $scope.settings.locale.frontend.language = {
              available: [],
              selected: null,
              slug: {}
            };
          }

          var frontend = $scope.settings.locale.frontend.language;

          // Set as selected locale if list empty
          if (!frontend.available || frontend.available.length === 0) {
            frontend.selected = item.code;
          }

          var codes = frontend.available.map(function(e) {
            return e.code;
          });

          // Add item if no already added
          if (codes.indexOf(item.code) === -1) {
            // Remove code from name
            item.name = item.name.replace(/\([a-z]+[_A-Za-z0-1]*\)/, '');

            frontend.available.push(item);
            frontend.slug[item.code] = item.code.substring(0, 2);
          }
        };

        /**
         * @function filterFromLanguages
         * @memberOf SettingsCtrl
         *
         * @description
         *   Filter Filter all selected languages
         *
         * @param {Integer } element to filter
         */
        $scope.filterFromLanguages = function(index) {
          if (!$scope.settings.translators[index].from) {
            return [];
          }

          var from = $scope.settings.translators[index].from;

          return $scope.settings.locale.frontend.language.available
            .filter(function(e) {
              return e.code !== from;
            });
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
          $scope.searching = true;

          var route = {
            name: $scope.routes.getLocales,
            params: { q: query }
          };

          return http.get(route).then(function(response) {
            $scope.searching = false;
            return response.data;
          });
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
          var frontend = $scope.settings.locale.frontend.language;
          var item     = frontend.available[index];

          // Remove slug
          delete frontend.slug[item.code];

          frontend.available.splice(index, 1);

          // No locales
          if (frontend.available.length === 0) {
            frontend.selected = null;
            return;
          }

          // No selected locale removed
          if (item.code !== frontend.selected) {
            return;
          }

          // Last language removed
          if (index >= frontend.available.length) {
            index = frontend.available.length - 1;
          }

          frontend.selected = frontend.available[index].code;
        };

        /**
         * @function getParameters
         * @memberOf SettingsCtrl
         *
         * @description
         *   Get all extra params for a translation service
         *
         * @param {Integer} index The index of the translation service
         */
        $scope.getParameters = function(index) {
          if (!$scope.extra.translation_services) {
            return [];
          }

          var translator = $scope.settings.translators[index].translator;
          var translators = $scope.extra.translation_services
            .filter(function(e) {
              return e.translator === translator;
            });

          if (translators.length === 0) {
            return [];
          }

          return translators[0].parameters;
        };

        /**
         * @function toggleDefaultTranslator
         * @memberOf SettingsCtrl
         *
         */
        $scope.toggleDefaultTranslator = function(index) {
          var current = $scope.settings.translators[index];

          current.default = true;

          angular.forEach($scope.settings.translators, function(translator, key) {
            if (key !== index && translator.to === current.to && translator.from === current.from) {
              delete translator.default;
            }
          });
        };

        $scope.post = function() {
          var data = {
            instance: angular.copy($scope.instance),
            settings: angular.copy($scope.settings)
          };

          data = cleaner.clean(data, true);

          // Save only locale codes
          if (data.settings.locale) {
            if (data.settings.locale.frontend.language.available instanceof Array) {
              var frontend = data.settings.locale.frontend.language.available
                .map(function(e) {
                  return e.code;
                });

              data.settings.locale.frontend.language.available = frontend;

              if (data.settings.locale.frontend.language.available.length === 0) {
                delete data.settings.locale.frontend.language.available;
              }
            }
          }

          return data;
        };

        $scope.pre = function() {
          if ($scope.settings.locale && !$scope.settings.locale.frontend.language.slug) {
            $scope.settings.locale.frontend.language.slug = {};
          }

          // Change value to string for old numeric timezones
          if ($scope.settings.locale && !isNaN(Number($scope.settings.locale.backend.timezone)) &&
              angular.isNumber(Number($scope.settings.locale.backend.timezone))) {
            $scope.settings.locale.backend.timezone = $scope.extra
              .timezones[Number($scope.settings.locale.backend.timezone)];
          }

          if ($scope.settings.locale && !angular.isArray($scope.settings.locale.frontend.language.available)) {
            return;
          }

          if ($scope.settings.locale) {
            $scope.settings.locale.frontend.language.available =
            $scope.settings.locale.frontend.language.available.map(function(e) {
              return { code: e, name: $scope.extra.locales.frontend[e] };
            });
          }

          angular.forEach($scope.settings.translators, function(value) {
            value.default = value.default === 'true';
          });
        };

        /**
         * @function addTranslator
         * @memberOf SettingsCtrl
         *
         * @description
         *   Add new translator.
         */
        $scope.addTranslator = function() {
          $scope.settings.translators
            .push({
              from: '',
              to: '',
              translator: ''
            });
        };

        /**
         * @function removeTranslator
         * @memberOf SettingsCtrl
         *
         * @description
         *   Removes a translator.
         *
         * @param {Integer} index The index of the input to remove.
         */
        $scope.removeTranslator = function(index) {
          $scope.settings.translators.splice(index, 1);
        };
      }
    ]);
})();
