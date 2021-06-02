(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SettingsCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires cleaner
     * @requires http
     * @requires messenger
     * @requires oqlEncoder
     *
     * @description
     *   Handles actions for settings configuration form.
     */
    .controller('SettingsCtrl', [
      '$controller', '$scope', 'cleaner', 'http', 'messenger', 'oqlEncoder',
      function($controller, $scope, cleaner, http, messenger, oqlEncoder) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf SettingsCtrl
         *
         * @description
         *  The instance properties that can be updated from settings.
         *
         * @type {Object}
         */
        $scope.instance = { country: null };

        /**
         * @memberOf SettingsCtrl
         *
         * @description
         *  Object for overlay-related flags.
         *
         * @type {Object}
         */
        $scope.overlay = {};

        /**
         * @memberOf SettingsCtrl
         *
         * @description
         *  The default sitemap values
         *
         * @type {Object}
         */
        $scope.default = {
          sitemap: [{
            perpage: 500,
            total: 100,
            album: 0,
            articles: 0,
            events: 0,
            images: 0,
            kiosko: 0,
            letters: 0,
            opinions: 0,
            polls: 0,
            tags: 0,
            videos: 0
              }]
        };

        /**
         * @memberOf SettingsCtrl
         *
         * @description
         *  The settings object with default values.
         *
         * @type {Object}
         */
        $scope.settings = {
          google_analytics: [
            {
              api_key: '',
              base_domain: '',
              custom_var: ''
            }
          ],
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
          rtb_files: [],
          theme_skin: 'default',
          translators: [],
          cookies: 'none',
          cmp_type: 'default',
          data_layer: []
        };

        /**
         * @function addRTBFile
         * @memberOf SettingsCtrl
         *
         * @description
         *   Adds an empty File to the answer list.
         */
        $scope.addRTBFile = function(file) {
          if ($scope.settings.rtb_files.indexOf(file) === -1) {
            $scope.settings.rtb_files.push(file);
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
          $scope.settings.google_analytics
            .push({
              api_key: '',
              base_domain: '',
              custom_var: ''
            });
        };

        /**
         * @function addDatalayerVariable
         * @memberOf SettingsCtrl
         *
         * @description
         *   Adds new pair key:value to Datalayer.
         */
        $scope.addDatalayerVariable = function() {
          $scope.settings.data_layer.push({ key: null, value: null });
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
         * @function expand
         * @memberOf SystemSettingsCtrl
         *
         * @description
         *   Creates a suggestion list basing on a file list.
         *
         * @param {String} domain The input domain.
         */
        $scope.getFiles = function(query) {
          oqlEncoder.configure({
            placeholder: {
              title: '[key] ~ "%[value]%"'
            }
          });

          var oql = oqlEncoder.getOql({ title: query, in_litter: 0, epp: 10 });

          var route = {
            name: 'api_v1_backend_attachment_get_list',
            params: { oql: oql }
          };

          $scope.searching = true;

          return http.get(route).then(function(response) {
            $scope.searching = false;

            return response.data.items.map(function(e) {
              return {
                id: e.pk_content,
                filename: e.path.replace(/^.*\/([^/]+)$/, '$1')
              };
            });
          }, function() {
            $scope.searching = false;
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
            name: 'api_v1_backend_settings_locale_list',
            params: { q: query }
          };

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
            $scope.instance = response.data.instance;
            $scope.extra    = response.data.extra;
            $scope.settings = angular.merge($scope.settings,
              response.data.settings);
            $scope.pre();
            $scope.loading = false;
          }, function() {
            $scope.loading = false;
          });
        };

        /**
         * @function removeFile
         * @memberOf SettingsCtrl
         *
         * @description
         *   Removes a file from settings.
         *
         * @param {String} name The file name.
         */
        $scope.removeFile = function(name) {
          $scope.settings[name] = null;
        };

        /**
         * @function removeRTBFile
         * @memberOf SettingsCtrl
         *
         * @description
         *   Removes one files from the file list given its index.
         *
         * @param {Integer} index The index of the file to remove.
         */
        $scope.removeRTBFile = function(index) {
          $scope.settings.rtb_files.splice(index, 1);
        };

        /**
         * @function removeGanalytics
         * @memberOf SettingsCtrl
         *
         * @description
         *   Removes a ga tracking code input.
         *
         * @param {Integer} index The index of the input to remove.
         */
        $scope.removeGanalytics = function(index) {
          $scope.settings.google_analytics.splice(index, 1);
        };

        /**
         * @function removeDatalayerVariable
         * @memberOf SettingsCtrl
         *
         * @description
         *   Removes a data layer variable input.
         *
         * @param {Integer} index The index of the input to remove.
         */
        $scope.removeDatalayerVariable = function(index) {
          $scope.settings.data_layer.splice(index, 1);
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
         * @function save
         * @memberOf SettingsCtrl
         *
         * @description
         *   Saves settings.
         */
        $scope.save = function() {
          var data = $scope.post();

          $scope.saving = true;

          http.put('api_v1_backend_settings_save', data)
            .then(function(response) {
              $scope.saving = false;
              messenger.post(response.data);
            }, function(response) {
              $scope.saving = false;
              messenger.post(response.data);
            });
        };

        /**
         * @function save
         * @memberOf SettingsCtrl
         *
         * @description
         *   Saves settings.
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

        /**
         * @function post
         * @memberOf SettingsCtrl
         *
         * @description
         *   Executes actions to adapt data from template to the webservice.
         *
         * @return {Object} Data ready to send to webservice.
         */
        $scope.post = function() {
          var data = {
            instance: angular.copy($scope.instance),
            settings: angular.copy($scope.settings)
          };

          data = cleaner.clean(data, true);

          // Save only locale codes
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

          if ($scope.settings.site_logo) {
            data.settings.site_logo = $scope.settings.site_logo;

            if (!(data.settings.site_logo instanceof File)) {
              data.settings.site_logo = $scope.settings.site_logo
                .replace($scope.extra.prefix, '');
            }
          }

          if ($scope.settings.mobile_logo) {
            data.settings.mobile_logo = $scope.settings.mobile_logo;

            if (!(data.settings.mobile_logo instanceof File)) {
              data.settings.mobile_logo = data.settings.mobile_logo
                .replace($scope.extra.prefix, '');
            }
          }

          if (data.settings.favico) {
            data.settings.favico = $scope.settings.favico;

            if (!(data.settings.favico instanceof File)) {
              data.settings.favico = data.settings.favico
                .replace($scope.extra.prefix, '');
            }
          }

          if (data.settings.sn_default_img) {
            data.settings.sn_default_img = $scope.settings.sn_default_img;

            if (!(data.settings.sn_default_img instanceof File)) {
              data.settings.sn_default_img = data.settings.sn_default_img
                .replace($scope.extra.prefix, '');
            }
          }

          return data;
        };

        /**
         * @function pre
         * @memberOf SettingsCtrl
         *
         * @description
         *   Executes actions to adapt data from webservice to the template.
         */
        $scope.pre = function() {
          // Backup some settings
          $scope.backup = {
            favico:               $scope.settings.favico,
            mobile_logo:          $scope.settings.mobile_logo,
            site_color:           $scope.settings.site_color,
            site_color_secondary: $scope.settings.site_color_secondary,
            site_logo:            $scope.settings.site_logo,
            sn_default_img:       $scope.settings.sn_default_img
          };

          if ($scope.settings.site_logo) {
            $scope.settings.site_logo =
              $scope.extra.prefix + $scope.settings.site_logo;
          }

          if ($scope.settings.mobile_logo) {
            $scope.settings.mobile_logo =
              $scope.extra.prefix + $scope.settings.mobile_logo;
          }

          if ($scope.settings.favico) {
            $scope.settings.favico =
              $scope.extra.prefix + $scope.settings.favico;
          }

          if ($scope.settings.sn_default_img) {
            $scope.settings.sn_default_img =
              $scope.extra.prefix + $scope.settings.sn_default_img;
          }

          if (!$scope.settings.locale.frontend.language.slug) {
            $scope.settings.locale.frontend.language.slug = {};
          }

          // Change value to string for old numeric timezones
          if (!isNaN(Number($scope.settings.locale.backend.timezone)) &&
              angular.isNumber(Number($scope.settings.locale.backend.timezone))) {
            $scope.settings.locale.backend.timezone = $scope.extra
              .timezones[Number($scope.settings.locale.backend.timezone)];
          }

          if (!angular.isArray($scope.settings.locale.frontend.language.available)) {
            return;
          }

          $scope.settings.locale.frontend.language.available =
            $scope.settings.locale.frontend.language.available.map(function(e) {
              return { code: e, name: $scope.extra.locales.frontend[e] };
            });

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

        // Update sitemap values from default
        $scope.$watch('settings.sitemap', function(nv, ov) {
          if (typeof nv !== 'undefined' && typeof ov === 'undefined') {
              $scope.settings.sitemap = angular.merge($scope.default.sitemap[0], $scope.settings.sitemap);
          }
        }, true);
      }
    ]);
})();
