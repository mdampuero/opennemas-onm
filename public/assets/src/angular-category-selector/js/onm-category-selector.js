(function() {
  'use strict';

  angular.module('onm.categorySelector', [ 'ui.select' ])

    /**
     * @ngdoc directive
     * @name  onmCategorySelector
     *
     * @description
     *   Directive to create show a category selector dynamically.
     */
    .directive('onmCategorySelector', [
      function() {
        return {
          restrict: 'E',
          scope: {
            ngModel:    '=',
            categories: '=',
            cssClass:      '@',
            labelString: '@',
            placeholderString: '@'
          },
          template: function() {
            return '<ui-select class="[% $parent.cssClass %]" name="category" ng-model="$parent.ngModel" theme="select2">' +
                '<ui-select-match placeholder="[% $parent.placeholderString %]">' +
                '  <strong ng-if="labelString">[% labelString %]: </strong>[% $select.selected.title %]' +
                '</ui-select-match>' +
                '<ui-select-choices group-by="groupCategories()" repeat="item.pk_content_category as item in categories| filter: { name: $select.search }">' +
                '  <div ng-bind-html="item.title | highlight: $select.search"></div>' +
                '</ui-select-choices>' +
              '</ui-select>';
          },
          link: function($scope) {
            if (!$scope.cssClass) {
              $scope.cssClass = 'form-control';
            }

            /**
             * @function groupCategories
             * @memberOf onmCategorySelector
             *
             * @description
             *   Groups categories in the ui-select.
             *
             * @param {Object} item The category to group.
             *
             * @return {String} The group name.
             */
            $scope.groupCategories = function() {
            console.log($scope.categories);

              var item = $scope.ngModel;

              if (!item) {
                return '';
              }

              var category = $scope.categories.filter(function(e) {
                return e.pk_content_category === item.category;
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

