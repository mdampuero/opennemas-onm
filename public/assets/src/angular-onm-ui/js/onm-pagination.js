(function() {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.ui.pagination
   *
   * @description
   *   The `onm.ui.pagination` module provides a directive and a controller to
   *   create pagination controls for lists.
   */
  angular.module('onm.ui.pagination', [])

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
    .directive('onmPagination', [
      '$compile', '$window',
      function($compile, $window) {
        return {
          controller: 'PaginationCtrl',
          restrict: 'E',
          scope: {
            itemsPerPage: '=',
            ngModel: '=',
            totalItems: '='
          },
          link: function($scope, $element, $attrs) {
            var paginationTpl = '<span class="pagination">' +
              '<span class="pagination-status" uib-tooltip="[% from %]-[% to %] ' + $window.strings.pagination.of + ' [% totalItems%]" tooltip-placement="bottom">' +
                '[% totalItems %]' +
              '</span>' +
              '<span class="pagination-epp" ng-hide="readOnly">' +
                '<button class="pagination-button" data-toggle="dropdown" type="button">' +
                  '<i class="fa fa-eye"></i>' +
                  '<span class="pagination-epp-number">' +
                    '[% itemsPerPage %]' +
                  '</span>' +
                  '<i class="fa fa-caret-down"></i>' +
                '</button>' +
                '<ul class="dropdown-menu">' +
                  '<li ng-class="{ \'active\': itemsPerPage === 10 }" ng-click="itemsPerPage = 10">' +
                    '<a href="#">' +
                       '10' +
                    '</a>' +
                  '</li>' +
                  '<li ng-class="{ \'active\': itemsPerPage === 25 }" ng-click="itemsPerPage = 25">' +
                    '<a href="#">' +
                       '25' +
                    '</a>' +
                  '</li>' +
                  '<li ng-class="{ \'active\': itemsPerPage === 50 }" ng-click="itemsPerPage = 50">' +
                    '<a href="#">' +
                       '50' +
                    '</a>' +
                  '</li>' +
                  '<li ng-class="{ \'active\': itemsPerPage === 100 }" ng-click="itemsPerPage = 100">' +
                    '<a href="#">' +
                       '100' +
                    '</a>' +
                  '</li>' +
                '</ul>' +
              '</span>' +
              '<span class="pagination-controls" ng-hide="readOnly">' +
                '<button class="pagination-button" ng-click="previous()" ng-disabled="isFirstPage()" type="button">' +
                  '<i class="fa fa-chevron-left"></i>' +
                '</button>' +
                '<span class="pagination-placeholder">' +
                  '<button class="pagination-fake-input" ng-click="setEdit(true)" ng-show="!edit" type="button">' +
                    '[% ngModel %] / [% totalPages %]' +
                  '</button>' +
                  '<input class="pagination-input" min="1" max="[% totalPages %]" ng-blur="setEdit(false)" ng-keypress="updatePage($event)" ng-show="edit" ng-model="page" type="number">' +
                '</span>' +
                '<button class="pagination-button" ng-click="next()" ng-disabled="isLastPage()" type="button">' +
                  '<i class="fa fa-chevron-right"></i>' +
                '</button>' +
              '</span>' +
            '</span>';

            var e = $compile(paginationTpl)($scope);

            $scope.readOnly = angular.isDefined($attrs.readonly);

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
    .controller('PaginationCtrl', [
      '$scope', '$timeout',
      function($scope, $timeout) {
        /**
         * @memberOf PaginationCtrl
         *
         * @description
         *  Whether to page edition is enabled.
         *
         * @type {Boolean}
         */
        $scope.edit = true;

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

        $scope.setEdit = function(value) {
          if ($('.page').is(':focus')) {
            return;
          }

          $scope.edit = value;

          if ($scope.edit) {
            $('.pagination-input').width(
              $('.pagination-fake-input').outerWidth());

            $timeout(function() {
              $('.pagination-input')[0].focus();
            }, 0);
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
          if (e.keyCode !== 13) {
            return;
          }

          if ($scope.page > 0 && $scope.page <= $scope.totalPages) {
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
          $scope.setEdit(false);
        });
      }
    ]);
})();
