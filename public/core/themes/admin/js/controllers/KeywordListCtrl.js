(function () {
 'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  KeywordListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires messenger
     * @requires oqlEncoder
     * @requires queryManager
     *
     * @description
     *   Controller for opinion list.
     */
    .controller('KeywordListCtrl', [
      '$controller', '$location', '$scope', 'http', 'messenger', 'oqlEncoder',
      function($controller, $location, $scope, http, messenger, oqlEncoder) {

        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentListCtrl', { $scope: $scope }));

      /**
       * Updates the array of contents.
       *
       * @param string route Route name.
       */
      $scope.list = function(route) {
        $scope.loading = 1;
        $scope.selected = { all: false, contents: [] };

        oqlEncoder.configure({
          placeholder: {
            pclave: 'pclave ~ "%[value]%"',
          }
        });

        var oql   = oqlEncoder.getOql($scope.criteria);
        var route = {
          name: $scope.route,
          params:  { oql: oql }
        };

        $location.search('oql', oql);

        http.get(route).then(function(response) {
          $scope.total = parseInt(response.data.total);
          $scope.contents         = response.data.results;
          $scope.map              = response.data.map;

          if (response.data.hasOwnProperty('extra')) {
            $scope.extra = response.data.extra;
          }

          // Disable spinner
          $scope.loading = 0;
        }, function () {
          $scope.loading = 0;

          messenger.post({
            message: 'Error while fetching data from backend',
            type:    'error'
          });
        });
      };
    }]);
})();
