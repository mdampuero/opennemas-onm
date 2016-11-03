(function () {
 'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  OpinionListCtrl
     *
     * @requires $controller
     * @requires $http
     * @requires $scope
     * @requires routing
     * @requires messenger
     * @requires oqlEncoder
     * @requires queryManager
     *
     * @description
     *   Controller for opinion list.
     */
    .controller('OpinionListCtrl', [
      '$controller', '$http', '$scope', 'routing', 'messenger', 'Encoder', 'queryManager',
      function($controller, $http, $scope, routing, messenger, Encoder, queryManager) {

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

        var url              = routing.generate(route);
        var processedFilters = Encoder.encode($scope.criteria);
        var filtersToEncode  = angular.copy($scope.criteria);

        delete filtersToEncode.content_type_name;

        queryManager.setParams(filtersToEncode, $scope.orderBy,
            $scope.pagination.epp, $scope.pagination.page);

        var postData = {
          elements_per_page: $scope.pagination.epp,
          page:              $scope.pagination.page,
          sort_by:           $scope.orderBy.name,
          sort_order:        $scope.orderBy.value,
          search:            processedFilters
        };

        $http.post(url, postData).then(function(response) {
          $scope.pagination.total = parseInt(response.data.total);
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
