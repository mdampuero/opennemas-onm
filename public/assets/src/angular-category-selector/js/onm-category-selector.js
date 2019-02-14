(function() {
  'use strict';

  angular.module('onm.categorySelector', [ 'onm.http', 'onm.localize', 'ui.select' ])

    /**
     * @ngdoc directive
     * @name  onmCategorySelector
     *
     * @description
     *   Directive to create category selector dynamically.
     */
    .directive('onmCategorySelector', [
      'http', 'localizer', 'linker',
      function(http, localizer, linker) {
        return {
          restrict: 'E',
          transclude: true,
          scope: {
            defaultValueText: '@',
            exclude: '=?',
            labelText: '@',
            locale: '=',
            ngModel: '=',
            placeholder: '@',
            selected: '=?'
          },
          template: function() {
            return '<ui-select class="[% cssClass %]" ng-required="required" name="category" ng-model="$parent.ngModel" theme="select2">' +
                '<ui-select-match placeholder="[% $parent.placeholder %]">' +
                '  <strong ng-if="labelText">[% labelText %]: </strong>[% $select.selected.title %]' +
                '</ui-select-match>' +
                '<ui-select-choices group-by="groupCategories" repeat="item.pk_content_category as item in (categories | filter: { title: $select.search })">' +
                '  <div ng-bind-html="item.title | highlight: $select.search"></div>' +
                '</ui-select-choices>' +
              '</ui-select>';
          },
          link: function($scope, elem, $attrs) {
            $scope.cssClass = $attrs.class ? $attrs.class : '';
            $scope.required = $attrs.required ? $attrs.required : false;

            http.get('api_v1_backend_categories_list').then(function(response) {
              response.data.items = response.data.items.filter(function(e) {
                return !$scope.exclude || $scope.exclude.length === 0 ||
                  $scope.exclude.indexOf(e.pk_content_category) === -1;
              });

              if (angular.isArray(response.data.items) &&
                  response.data.items.length > 0 &&
                  response.data.items[0].pk_content_category !== null) {
                response.data.items.unshift({
                  pk_content_category: null,
                  title: $scope.defaultValueText
                });
              }

              var lz = localizer.get(response.data.extra.locale);

              // Localize items
              $scope.categories = lz.localize(response.data.items,
                response.data.extra.keys, response.data.extra.locale);

              // Initialize linker
              if (!$scope.linker) {
                $scope.linker = linker.get(response.data.extra.keys,
                  response.data.extra.locale.default, $scope);
              }

              // Link original and localized items
              $scope.linker.setKey($scope.locale);
              $scope.linker.link(response.data.items, $scope.categories);
            });

            // Updates the selected item when model or categories change
            $scope.$watch('[ categories, ngModel ]', function() {
              if (!$scope.ngModel || !$scope.categories) {
                $scope.selected = null;
                return;
              }

              var selected = $scope.categories.filter(function(e) {
                return e.pk_content_category === $scope.ngModel;
              });

              $scope.selected = selected.length > 0 ? selected[0] : null;
            }, true);

            // Updates linker when locale changes
            $scope.$watch('locale', function(nv, ov) {
              if (nv === ov || !$scope.linker) {
                return;
              }

              $scope.linker.setKey(nv);
              $scope.linker.update();
            }, true);

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
                return '';
              }

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

