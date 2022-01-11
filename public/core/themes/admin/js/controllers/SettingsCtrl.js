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
        $scope.sitemap = {
          perpage: 500,
          total: 100,
          album: 0,
          article: 0,
          event: 0,
          photo: 0,
          kiosko: 0,
          letter: 0,
          opinion: 0,
          poll: 0,
          tag: 0,
          video: 0
        };

        /**
         * @memberOf SettingsCtrl
         *
         * @description
         *  The default value for the sitemaps flag.
         *
         * @type {boolean}
         */
        $scope.flags.show = false;

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
         * @function list
         * @memberOf SettingsCtrl
         *
         * @description
         *   Lists all settings.
         */
        $scope.list = function() {
          $scope.loading = true;

          http.get($scope.routes.getConfig).then(function(response) {
            $scope.instance = response.data.instance;
            $scope.extra    = response.data.extra;
            $scope.settings = angular.merge($scope.settings, response.data.settings);

            $scope.pre();

            $scope.loading = false;
          }, function() {
            $scope.loading = false;
          });
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

          http.put($scope.routes.saveConfig, data)
            .then(function(response) {
              // Remove the sitemaps from the array if the sitemap configuration has been changed
              if ($scope.flags.sitemap) {
                $scope.extra.sitemaps.items = [];
              }
              $scope.saving = false;
              messenger.post(response.data);
            }, function(response) {
              $scope.saving = false;
              messenger.post(response.data);
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
            logo_favico:          $scope.settings.logo_favico,
            logo_simple:          $scope.settings.logo_simple,
            site_color:           $scope.settings.site_color,
            site_color_secondary: $scope.settings.site_color_secondary,
            logo_default:         $scope.settings.logo_default,
            logo_embed:           $scope.settings.logo_embed
          };
        };
      }
    ]);
})();
