(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  InternalSettingsCtrl
     *
     * @requires $controller
     * @requires $scope
     */
    .controller('InternalSettingsCtrl', [
      '$controller', '$scope', 'http', 'oqlEncoder',
      function($controller, $scope, http, oqlEncoder) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('SettingsCtrl', { $scope: $scope }));
        $scope.settings = {
          rtb_files: [],
        };
        $scope.routes = {
          saveConfig: 'api_v1_backend_settings_internal_save',
          getConfig: 'api_v1_backend_settings_internal_list'
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
      }
    ]);
})();
