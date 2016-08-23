(function () {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.pagination
   *
   * @description
   *   The `onm.pagination` module provides a directive and a controller to
   *   create pagination controls for lists.
   */
  angular.module('onm.pagination', [])
    /**
     * @ngdoc directive
     * @name  onmPagination
     *
     * @requires $compile
     *
     * @description
     *   Directive to create and display the pagination controls.
     *
     *  ###### Attributes:
     *  - **`items-per-page`**: The number of items per page. (Required)
     *  - **`ng-model`**: The current page object. (Required)
     *  - **`total-items`**: The number of items to paginate. (Required)
     *
     * @example
     * <onm-pagination items-per-page="pagination.epp" ng-model="pagination.page" total-items="pagination.total">
     * </onm-pagination>
     */
    .directive('onmPagination', ['$compile',
      function ($compile) {
        return {
          controller: 'PaginationCtrl',
          restrict: 'E',
          scope: {
            itemsPerPage: '=',
            ngModel: '=',
            totalItems: '='
          },
          link: function ($scope, $element) {
            var paginationTpl = '<span class="pagination-status">' +
              '[% from %]-[% to %] of [% totalItems %]' +
            '</span>' +
            '<div class="pagination-controls">' +
              '<button class="btn btn-white" ng-click="previous()" ng-disabled="isFirstPage()" type="button">' +
                '<i class="fa fa-chevron-left"></i>' +
              '</button>' +
              '<input min="1" max="[% totalPages %]" ng-keypress="updatePage($event)" ng-model="page" type="number">' +
              '<button class="btn btn-white" ng-click="next()" ng-disabled="isLastPage()" type="button">' +
                '<i class="fa fa-chevron-right"></i>' +
              '</button>' +
            '</div>';

            var e = $compile(paginationTpl)($scope);
            $element.replaceWith(e);
          }
        };
      }
    ])

    /**
     * @ngdoc controller
     * @name  PaginationCtrl
     *
     * @description
     *   Controller to handle pagination actions.
     *
     * @requires $scope
     */
    .controller('PaginationCtrl', ['$scope',
      function ($scope) {
        /**
         * @memberof PaginationCtrl
         *
         * @description
         *   Proxy variable for the current page.
         *
         * @type {Integer}
         */
        $scope.page = $scope.ngModel;

        /**
         * @function isFirstPage
         * @memberof PaginationCtrl
         *
         * @description
         *   Checks if the current page is the first page.
         *
         * @return {Boolean} True if the current page is the first page. Otherwise,
         *                   returns false.
         */
        $scope.isFirstPage = function() {
          return $scope.ngModel === 1;
        };

        /**
         * @function isLastPage
         * @memberof PaginationCtrl
         *
         * @description
         *   Checks if the current page is the last page.
         *
         * @return {Boolean} True if the current page is the last page. Otherwise,
         *                   returns false.
         */
        $scope.isLastPage = function() {
          return $scope.ngModel === $scope.totalPages;
        };

        /**
         * @function next
         * @memberof PaginationCtrl
         *
         * @description
         *   Increases the current page value by 1.
         */
        $scope.next = function() {
          if ($scope.ngModel < $scope.totalPages) {
            $scope.ngModel = $scope.ngModel + 1;
          }
        };

        /**
         * @function previous
         * @memberof PaginationCtrl
         *
         * @description
         *   Decreases the current page value by 1.
         */
        $scope.previous = function() {
          if ($scope.ngModel > 1) {
            $scope.ngModel = $scope.ngModel - 1;
          }
        };

        /**
         * @function updatePage
         * @memberOf PaginationCtrl
         *
         * @description
         *   Update the current page on enter press.
         *
         * @param {Object} e The event object.
         */
        $scope.updatePage = function(e) {
          if (e.keyCode === 13 && $scope.page > 0 &&
              $scope.page <= $scope.totalPages) {
            $scope.ngModel = $scope.page;
          }
        };

        // Updates pagination values when the current page changes.
        $scope.$watch('[ ngModel, itemsPerPage, totalItems ]', function() {
          $scope.from = 1;
          $scope.to   = $scope.totalItems;
          $scope.totalPages = Math.ceil($scope.totalItems / $scope.itemsPerPage);

          if (($scope.ngModel - 1) * $scope.itemsPerPage > 0) {
            $scope.from = ($scope.ngModel - 1) * $scope.itemsPerPage;
          }

          if ($scope.ngModel * $scope.itemsPerPage < $scope.totalItems) {
            $scope.to = $scope.ngModel * $scope.itemsPerPage;
          }

          $scope.page = $scope.ngModel;
        });
      }
    ]);
})();
