(function() {
  'use strict';

  angular.module('ManagerApp.controllers')

    /**
     * @ngdoc controller
     * @name SitemapListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $timeout
     * @requires http
     * @requires messenger

     * @description
     *   Handles all actions in users listing.
     */
    .controller('SitemapListCtrl', [
      '$controller', '$scope', '$timeout', 'http', 'messenger',
      function($controller, $scope, $timeout, http, messenger) {
        /**
         * @function list
         * @memberOf InstanceListCtrl
         *
         * @description
         *   Reloads the list.
         */
        $scope.list = function() {
          $scope.loading = 1;

          var route = {
            name: 'manager_ws_sitemap_list'
          };

          return http.get(route).then(function(response) {
            $scope.items = response.data;
          });
        };

        $scope.save = function() {
          http.post('manager_ws_sitemap_save', $scope.items).then(function(response) {
            $scope.saving = 0;
            messenger.post(response.data);
          }, function() {
            $scope.saving = 0;
          });
        };

        $scope.list();
      }
    ]);
})();
