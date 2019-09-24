(function() {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.ui.categorySelector
   *
   * @requires onm.http
   * @requires onm.localize
   * @requires ui.select
   *
   * @description
   *   The `onm.ui.categorySelector` module provides a directive to
   *   automagically generate a category selector with multilanguage support.
   */
  angular.module('onm.ui.categorySelector', [ 'onm.http', 'onm.localize', 'ui.select' ])

    /**
     * @ngdoc directive
     * @name  onmCategorySelector
     *
     * @requires http
     * @requires linker
     * @requires localizer
     *
     * @description
     *   Directive to create category selector dynamically.
     */
    .directive('onmCategorySelector', [
      'http', 'linker', 'localizer',
      function(http, linker, localizer) {
        return {
          restrict: 'E',
          transclude: true,
          scope: {
            defaultValueText: '@',
            exclude: '=?',
            exportModel: '=?',
            hideArchived: '@',
            labelText: '@',
            locale: '=',
            multiple: '@',
            ngModel: '=',
            placeholder: '@',
            selected: '=?',
            selectedText: '@'
          },
          template: function() {
            return '<ui-select class="[% cssClass %]" ng-required="required" ng-if="!multiple" ng-model="$parent.$parent.exportModel" theme="select2">' +
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

            // Force integers in ngModel on initialization
            if ($scope.ngModel) {
              $scope.ngModel = !$scope.multiple ?
                parseInt($scope.ngModel) :
                $scope.ngModel.map(function(e) {
                  return parseInt(e);
                });
            }

            var route = { name: 'api_v1_backend_category_get_list' };

            if ($scope.hideArchived) {
              route.params = { oql: 'archived = 0' };
            }

            http.get(route).then(function(response) {
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

            /**
             * @function updateNgModel
             * @memberOf onmCategorySelector
             *
             * @description
             *   Updates ngModel basing on current exportModel.
             */
            $scope.updateNgModel = function() {
              if (!$scope.exportModel) {
                return;
              }

              var newValue = !$scope.multiple ?
                $scope.exportModel.pk_content_category :
                $scope.exportModel.map(function(e) {
                  return e.pk_content_category;
                });

              // Do not update if both values are null/undefined or equal
              if (!newValue && !$scope.ngModel ||
                  angular.equals($scope.ngModel, newValue)) {
                return;
              }

              // Mark field in form as dirty
              if ($scope.$parent && $scope.$parent.form &&
                  $scope.$parent.form.category) {
                $scope.$parent.form.category.$setDirty(true);
              }

              $scope.ngModel = newValue;
            };

            /**
             * @function updateExportModel
             * @memberOf onmCategorySelector
             *
             * @description
             *   Updates exportModel basing on current ngModel.
             */
            $scope.updateExportModel = function() {
              if (!$scope.categories) {
                return;
              }

              var needle = $scope.multiple ? [] : [ null ];

              if ($scope.ngModel) {
                needle = $scope.multiple ? $scope.ngModel : [ $scope.ngModel ];
              }

              var found = $scope.categories.filter(function(e) {
                return needle.indexOf(e.pk_content_category) !== -1;
              });

              if (found.length === 0) {
                $scope.exportModel = $scope.multiple ? [] : null;
                return;
              }

              var newValue = $scope.multiple ? found : found[0];

              if (!angular.equals($scope.exportModel, newValue)) {
                $scope.exportModel = newValue;
              }
            };

            // Try to select an option when categories loaded
            $scope.$watch('categories', function(nv) {
              if (!nv) {
                return;
              }

              $scope.updateExportModel();
            });

            // Updates external model when internal model changes
            $scope.$watch('exportModel', function() {
              $scope.updateNgModel();
            }, true);

            // Updates internal model when external model changes
            $scope.$watch('ngModel', function() {
              $scope.updateExportModel();
            }, true);
          },
        };
      }
    ]);
})();
