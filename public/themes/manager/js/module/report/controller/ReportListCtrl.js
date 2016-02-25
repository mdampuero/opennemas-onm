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
      '$scope', 'webStorage', 'data',
      function ($scope, webStorage, data) {
        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {};

        /**
         * List of available users.
         *
         * @type {Object}
         */
        $scope.items = data.results;

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
      }
    ]);
})();
