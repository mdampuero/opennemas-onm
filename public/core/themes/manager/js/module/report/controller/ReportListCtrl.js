(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  ReportListCtrl
     *
     * @requires $scope
     * @requires webStorage
     *
     * @description
     *   Handles actions for report listing.
     */
    .controller('ReportListCtrl', [
      '$scope', 'webStorage', 'itemService',
      function ($scope, webStorage, itemService) {
        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {};

        $scope.list = function() {
          itemService.list('manager_ws_reports_list', {}).then(
            function(response) {
              $scope.items = response.data.results;
            }
          );
        };

        /**
         * Marks variables to delete for garbage collector;
         */
        $scope.$on('$destroy', function() {
          $scope.criteria = null;
          $scope.items    = null;
        });

        if (webStorage.local.get('token')) {
          $scope.token = webStorage.local.get('token');
        }

        $scope.list();
      }
    ]);
})();
