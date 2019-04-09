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
            exportModel: '=?',
            labelText: '@',
            locale: '=',
            multiple: '@',
            ngModel: '=',
            placeholder: '@',
            selected: '=?',
            selectedText: '@'
          },
          template: function() {
            return '<ui-select class="[% cssClass %]" ng-required="required" name="category" ng-if="!multiple" ng-model="$parent.$parent.exportModel" theme="select2">' +
              '<ui-select-match placeholder="[% $parent.placeholder %]">' +
              '  <strong ng-if="labelText">[% labelText %]: </strong>[% $select.selected.title %]' +
              '</ui-select-match>' +
              '<ui-select-choices group-by="groupCategories" repeat="item in (categories | filter: { title: $select.search })">' +
              '  <div ng-bind-html="item.title | highlight: $select.search"></div>' +
              '</ui-select-choices>' +
            '</ui-select>' +
            '<div class="[% cssClass %] ui-select-container select2 select2-container" ng-if="multiple">' +
              '<a class="select2-choice ui-select-match" data-toggle="dropdown">' +
                '<span class="select2-chosen">' +
                  '<strong ng-if="labelText">[% labelText %]:</strong>' +
                  '<span ng-show="!ngModel || ngModel.length === 0">' +
                    ' [% placeholder %]' +
                  '</span>' +
                  '<span ng-show="ngModel && ngModel.length !== 0">' +
                    ' [% ngModel.length ? ngModel.length : 1 %] [% selectedText %]' +
                  '</span>' +
                  '<span class="select2-arrow"><b></b></span>' +
                '</span>' +
              '</a>' +
              '<div class="block ui-select-dropdown select2-with-searchbox dropdown-menu keepopen">' +
                '<a class="select2-btn" ng-click="toggleAll()">' +
                  '[% defaultValueText %]' +
                '</a>' +
                '<ul class="ui-select-choices select2-results">' +
                  '<li class="ui-select-choices-group select2-result-with-children" ng-repeat="(key, items) in categories | groupBy: \'fk_content_category\'">' +
                    '<div class="ui-select-choices-group-label select2-result-label" ng-show="groupCategories(items[0])">' +
                      '[% groupCategories(items[0]) %]' +
                    '</div>' +
                    '<ul class="select2-result-single select2-result-sub">' +
                      '<li class="ui-select-choices-row" ng-repeat="item in items" ng-class="{ \'select2-highlighted\': isSelected(item) }" ng-click="toggle(item)">' +
                        '<div class="select2-result-label ui-select-choices-row-inner">' +
                          '[% item.title %]' +
                        '</div>' +
                      '</li>' +
                    '</ul>' +
                  '</li>' +
                '</ul>' +
              '</div>' +
            '</div>' +
            '<input id="category" name="category" type="hidden" value="[% ngModel %]">';
          },
          link: function($scope, elem, $attrs) {
            $scope.cssClass     = $attrs.class ? $attrs.class : '';
            $scope.multiple     = $attrs.multiple;
            $scope.required     = $attrs.required ? $attrs.required : false;
            $scope.selectedText = $scope.selectedText || 'selected';

            http.get('api_v1_backend_categories_list').then(function(response) {
              response.data.items = response.data.items.filter(function(e) {
                return !$scope.exclude || $scope.exclude.length === 0 ||
                  $scope.exclude.indexOf(e.pk_content_category) === -1;
              });

              if (!$scope.multiple && angular.isArray(response.data.items) &&
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

              if ($scope.multiple && $scope.ngModel &&
                !angular.isArray($scope.ngModel)) {
                $scope.ngModel = [ $scope.ngModel ];
              }

              $scope.selected = $scope.categories.filter(function(e) {
                return e.pk_content_category === $scope.ngModel;
              });
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

            /**
             * @function isSelected
             * @memberOf onmCategorySelector
             *
             * @description
             *   Checks if an item is selected.
             *
             * @param {Object} item The item to check.
             *
             * @return {Boolean} True if the item is selected. False otherwise.
             */
            $scope.isSelected = function(item) {
              if (!item || !angular.isArray($scope.ngModel)) {
                return false;
              }

              return $scope.ngModel.indexOf(item.pk_content_category) !== -1 ||
                $scope.ngModel.indexOf(
                  item.pk_content_category.toString()) !== -1;
            };

            /**
             * @function toggle
             * @memberOf onmCategorySelector
             *
             * @description
             *   Adds/removes an item from ngModel.
             *
             * @param {Object} item The item to toggle.
             */
            $scope.toggle = function(item) {
              if (!$scope.ngModel) {
                $scope.ngModel = [];
              }

              var position = $scope.ngModel.indexOf(item.pk_content_category);

              if (position < 0) {
                $scope.ngModel.push(item.pk_content_category);
                $scope.exportModel.push(item);
              } else {
                $scope.ngModel.splice(position, 1);
                $scope.exportModel.splice(position, 1);
              }
            };

            /**
             * @function toggleAll
             * @memberOf onmCategorySelector
             *
             * @description
             *   Adds/removes all items from ngModel.
             */
            $scope.toggleAll = function() {
              if (!$scope.exportModel) {
                $scope.exportModel = [];
              }

              if ($scope.exportModel.length !== $scope.categories.length) {
                $scope.exportModel = angular.copy($scope.categories);
              } else {
                $scope.exportModel = [];
              }
            };

            // Updates internal and external models when something changes
            $scope.$watch('[ categories, exportModel, ngModel ]', function(nv, ov) {
              if (!$scope.categories) {
                return;
              }

              // Force integers in ngModel on initialization
              if (ov[2] !== nv[2] && angular.isArray(nv[2])) {
                $scope.ngModel = nv[2].map(function(e) {
                  return parseInt(e);
                });
              }

              // Update ngModel when selected category changes
              if (angular.isDefined(nv[1])) {
                $scope.ngModel = !angular.isArray(nv[1]) ?
                  nv[1].pk_content_category :
                  nv[1].map(function(e) {
                    return parseInt(e.pk_content_category);
                  });
                return;
              }

              // Change category only when ngModel initialized
              if (!$scope.exportModel) {
                $scope.exportModel = !angular.isArray(nv[2]) ?
                  $scope.categories.filter(function(e) {
                    return !$scope.ngModel && e.pk_content_category === null ||
                      e.pk_content_category === $scope.ngModel;
                  })[0] :
                  $scope.categories.filter(function(e) {
                    return $scope.ngModel.indexOf(e.pk_content_category) !== -1;
                  });
              }
            }, true);
          },
        };
      }
    ]);
})();

