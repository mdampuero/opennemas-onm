(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SitemapSettingsCtrl
     *
     * @requires $controller
     * @requires $scope
     */
    .controller('SitemapSettingsCtrl', [
      '$controller', '$scope', 'http', 'messenger',
      function($controller, $scope, http, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('SettingsCtrl', { $scope: $scope }));
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
         *  The default criteria values for the sitemap.
         *
         * @type {Object}
         */
        $scope.criteria = {
          year:  '',
          month: '',
          page: null
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

        $scope.routes = {
          saveConfig: 'api_v1_backend_settings_sitemap_save',
          getConfig: 'api_v1_backend_settings_sitemap_list'
        };

        /**
         * @function filterSitemaps
         * @memberOf SettingsCtrl
         *
         * @description
         *  Filter the sitemap files.
         *
         * @param {Array}
         */
        $scope.filterSitemaps = function(criteria) {
          return function(item) {
            var obj   = {};
            var array = item.split('.').slice(1, 4);

            obj.year  = array[0];
            obj.month = array[1];
            obj.page  = array[2];

            for (var prop in criteria) {
              if (criteria[prop] !== null && criteria[prop] !== '' && criteria[prop].toString() !== obj[prop]) {
                return false;
              }
            }

            return true;
          };
        };

        /**
         * @function removeSitemaps
         * @memberOf SettingCtrl
         *
         * @description
         *  Remove sitemaps.
         */
        $scope.removeSitemaps = function() {
          http.delete('api_v1_backend_sitemap_delete', $scope.criteria)
            .then(function(response) {
              // Remove the sitemaps in the extras
              if (response.data.deleted.length > 0) {
                $scope.extra.sitemaps.items = $scope.extra.sitemaps.items.filter(function(sitemap) {
                  return response.data.deleted.indexOf(sitemap) < 0;
                });
              }
              messenger.post(response.data.message);
            }, function(response) {
              messenger.post(response.data.message);
            });
        };

        // Update sitemap values from default
        $scope.$watch('settings.sitemap', function(nv, ov) {
          if (nv && nv !== ov && !$scope.flags.sitemap) {
            $scope.flags.sitemap = true;
          }

          if (!nv || ov) {
            return;
          }

          $scope.settings.sitemap = angular.merge($scope.sitemap, $scope.settings.sitemap);
        }, true);
      }
    ]);
})();
