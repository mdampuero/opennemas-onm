(function() {
  'use strict';

  angular.module('onm.categorySelector', [ 'ui.select' ])

    /**
     * $scope.categories
     * @ngdoc directive
     * @name  onmTag
     *
     * @description
     *   Directive to create forms dynamically.
     */
    .directive('onmCategorySelector', [
      function() {
        return {
          restrict: 'E',
          scope: {
            ngModel:    '=',
            categories: '=',
            class:      '=',
            labelString: '='
          },
          template: function() {
            return '<ui-select name="category" ng-model="$parent.ngModel" theme="select2">' +
                '<ui-select-match>' +
                '  <strong ng-if="labelString">[% labelString %]</strong>[% $select.selected.title %]' +
                '</ui-select-match>' +
                '<ui-select-choices group-by="groupCategories()" repeat="item.pk_content_category as item in categories| filter: { name: $select.search }">' +
                '  <div ng-bind-html="item.title | highlight: $select.search"></div>' +
                '</ui-select-choices>' +
              '</ui-select>';
          },
          link: function($scope) {
            /**
             * @function groupCategories
             * @memberOf ArticleListCtrl
             *
             * @description
             *   Groups categories in the ui-select.
             *
             * @param {Object} item The category to group.
             *
             * @return {String} The group name.
             */
            $scope.groupCategories = function() {
              var item = $scope.ngModel;

              var category = $scope.categories.filter(function(e) {
                return e.pk_content_category === item.fk_content_category;
              });

              if (category.length > 0 && category[0].pk_content_category) {
                return category[0].title;
              }

              return '';
            };
          },
        };
      }
    ]);
})();

