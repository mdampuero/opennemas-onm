(function() {
  'use strict';

  angular.module('onm.categorySelector', [ 'ui.select' ])

    /**
     * @ngdoc directive
     * @name  onmCategorySelector
     *
     * @description
     *   Directive to create category selector dynamically.
     */
    .directive('onmCategorySelector', [
      function() {
        return {
          restrict: 'E',
          transclude: true,
          scope: {
            ngModel:    '=',
            categories: '=',
            labelText: '@',
            placeholderText: '@',
            defaultValueText: '@'
          },
          template: function() {
            return '<ui-select ng-required="required" class="[% cssClass %]" name="category" ng-model="$parent.ngModel" theme="select2">' +
                '<ui-select-match placeholder="[% $parent.placeholderText %]">' +
                '  <strong ng-if="labelText">[% labelText %]: </strong>[% $select.selected.title %]' +
                '</ui-select-match>' +
                '<ui-select-choices group-by="groupCategories()" ' +
                  'repeat="item.pk_content_category as item in (loadCategories()| filter: { title: $select.search }) track by item.pk_content_category">' +
                '  <div ng-bind-html="item.title | highlight: $select.search"></div>' +
                '</ui-select-choices>' +
              '</ui-select>';
          },
          link: function($scope, elem, $attrs) {
            $scope.categories = [];
            $scope.cssClass = $attrs.class ? $attrs.class : 'form-control';
            $scope.required = $attrs.required ? $attrs.required : false;

            /**
             * @function loadCategories
             * @memberOf onmCategorySelector
             *
             * @description
             *   Returns the list of categories adding the default at the top
             *
             * @return {Array} The list of categories.
             */
            $scope.loadCategories = function() {
              if (!angular.isArray($scope.categories) || $scope.categories.length === 0) {
                return [];
              }

              if ($scope.categories[0].pk_content_category !== null) {
                $scope.categories.unshift({
                  pk_content_category: null,
                  title: $scope.defaultValueText
                });
              }

              return $scope.categories;
            };

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
            $scope.groupCategories = function(item) {
              if (!item) {
                return;
              }

              var category = $scope.categories.filter(function(e) {
                return e.pk_content_category === item.category;
              });

              if (category.length > 0 && category[0].pk_content_category) {
                return category[0].title;
              }
            };
          },
        };
      }
    ]);
})();

