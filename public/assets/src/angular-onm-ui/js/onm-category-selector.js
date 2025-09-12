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
      '$q', 'http', 'linker', 'localizer',
      function($q, http, linker, localizer) {
        return {
          restrict: 'E',
          transclude: true,
          scope: {
            defaultValueText: '@',
            exclude: '=?',
            exportModel: '=?',
            showVisible: '@',
            labelText: '@',
            locale: '=',
            multiple: '@',
            name: '@',
            ngModel: '=',
            placeholder: '@',
            position: '@',
            selectedText: '@'
          },
          template: function() {
            return '<ui-select class="[% cssClass %]" ng-if="!multiple" ng-model="$parent.$parent.exportModel" theme="select2">' +
              '<ui-select-match placeholder="[% $parent.placeholder %]">' +
              '  <strong ng-if="labelText">[% labelText %]: </strong>[% $select.selected.title %]' +
              '</ui-select-match>' +
              '<ui-select-choices group-by="groupCategories" position="[% $parent.$parent.position %]" repeat="item in (categories | filter: { title: $select.search })">' +
              '  <div ng-bind-html="item.title | highlight: $select.search"></div>' +
              '</ui-select-choices>' +
            '</ui-select>' +
            '<div class="[% cssClass %] ui-select-container select2 select2-container direction-[% position %]" ng-class="{ dropup: position === \'up\' }" ng-if="multiple">' +
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
                  '<li class="ui-select-choices-group select2-result-with-children" ng-repeat="(key, items) in categories | groupBy: \'parent_id\'">' +
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
            '<input id="category" name="category" ng-model="ngModel" ng-required="required" type="hidden" value="[% ngModel %]">';
          },
          link: function($scope, elem, $attrs) {
            $scope.cssClass     = $attrs.class ? $attrs.class : '';
            $scope.multiple     = $attrs.multiple;
            $scope.position     = $scope.position ? $scope.position : 'down';
            $scope.required     = $attrs.required ? $attrs.required : false;
            $scope.selectedText = $scope.selectedText || 'selected';

            if ($scope.ngModel) {
              // Convert value to array when multiple and not an array
              if ($scope.multiple && !($scope.ngModel instanceof Array)) {
                $scope.ngModel = [ $scope.ngModel ];
              }

              // Force integers in ngModel on initialization
              $scope.ngModel = !$scope.multiple ?
                parseInt($scope.ngModel) :
                $scope.ngModel.map(function(e) {
                  return parseInt(e);
                });
            }

            var route = {
              name: 'api_v1_backend_category_get_list',
              params: { oql: 'visible = 1' }
            };

            if ($scope.showVisible) {
              delete route.params;
            }

            http.get(route).then(function(response) {
              response.data.items = response.data.items.filter(function(e) {
                return !$scope.exclude || $scope.exclude.length === 0 ||
                  $scope.exclude.indexOf(e.id) === -1;
              });

              $scope.data = response.data;

              if (!$scope.multiple) {
                $scope.addDefaultValue($scope.data.items);
              }

              $scope.localize($scope.data.items, $scope.data.extra);
            });

            /**
             * @function addDefaultValue
             * @memberOf onmCategorySelector
             *
             * @description
             *   Adds a default value at the beginning of the list of items.
             *
             * @param {Array} items The list of items.
             */
            $scope.addDefaultValue = function(items) {
              if (angular.isArray(items) && items.length > 0 &&
                  items[0].id !== null) {
                items.unshift({
                  id: null,
                  title: $scope.defaultValueText
                });
              }
            };

            /**
             * @function cleanModel
             * @memberOf onmCategorySelector
             *
             * @description
             *   Remove from ngModel category ids not found in the list of
             *   categories.
             */
            $scope.cleanModel = function() {
              var ids = $scope.categories.map(function(e) {
                return e.id;
              });

              // Reset ngModel as id is not found in the list of categories
              if (!$scope.multiple && ids.indexOf($scope.ngModel) === -1) {
                $scope.ngModel = null;
                return;
              }

              if ($scope.ngModel instanceof Array) {
                // Keep only ids found in the list of categories
                $scope.ngModel = $scope.ngModel.filter(function(e) {
                  return ids.indexOf(e) !== -1;
                });
              }
            };

            /**
             * @function findMissingCategories
             * @memberOf onmCategorySelector
             *
             * @description
             *   Returns the list of ids in model not present in the list of
             *   categories.
             *
             * @return {Array} The lisf of ids in model not present in the list
             *                 of categories.
             */
            $scope.findMissingCategories = function() {
              if (!$scope.categories) {
                return;
              }

              // Selected default value when no value selected
              if (!$scope.ngModel) {
                $scope.updateExportModel();
                return;
              }

              var model = $scope.ngModel instanceof Array ?
                $scope.ngModel : [ $scope.ngModel ];

              var ids = $scope.categories.map(function(e) {
                return e.id;
              });

              var missed = model.filter(function(e) {
                return ids.indexOf(e) === -1;
              });

              if (missed.length === 0) {
                $scope.updateExportModel();
                return;
              }

              var missing = [];

              for (var i = 0; i < missed.length; i++) {
                var route = {
                  name: 'api_v1_backend_category_get_item',
                  params: { id: missed[i] }
                };

                missing[i] = http.get(route).then(function(response) {
                  return response.data.item;
                });
              }

              $q.all(missing).then(function(items) {
                $scope.data.items = $scope.data.items[0].id === null ?
                  [ $scope.data.items.shift() ].concat(items, $scope.data.items) :
                  items.concat($scope.data.items);

                $scope.localize($scope.data.items, $scope.data.extra);
                $scope.cleanModel();
                $scope.updateExportModel();
              }, function() {
                $scope.cleanModel();
                $scope.updateExportModel();
              });
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
                return '';
              }

              var category = $scope.categories.filter(function(e) {
                return e.id === item.parent_id;
              });

              if (category.length > 0 && category[0].id) {
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

              return $scope.ngModel.indexOf(item.id) !== -1 ||
                $scope.ngModel.indexOf(
                  item.id.toString()) !== -1;
            };

            /**
             * @function localize
             * @memberOf onmCategorySelector
             *
             * @description
             *   Localizes the list of items based on the information included
             *   in extra parameter.
             *
             * @param {Array}  items The list of items to localize.
             * @param {Object} extra The information used during localization.
             */
            $scope.localize = function(items, extra) {
              var lz = localizer.get(extra.locale);

              // Localize items
              $scope.categories = lz.localize(items, extra.keys, extra.locale);

              // Initialize linker
              if (!$scope.linker) {
                $scope.linker = linker.get(extra.keys, extra.locale.default, $scope);
              }

              // Link original and localized items
              $scope.linker.setKey($scope.locale);
              $scope.linker.link(items, $scope.categories);
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

              var position = $scope.ngModel.indexOf(item.id);

              if (position < 0) {
                $scope.ngModel.push(item.id);
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
                $scope.exportModel.id :
                $scope.exportModel.map(function(e) {
                  return e.id;
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
                needle = $scope.ngModel instanceof Array ?
                  $scope.ngModel : [ $scope.ngModel ];
              }

              var found = $scope.categories.filter(function(e) {
                return needle.indexOf(e.id) !== -1;
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

            // Try to find missing categories when categories loaded
            $scope.$watch('categories', function(nv, ov) {
              if (!ov && nv) {
                $scope.findMissingCategories();
              }
            });

            // Updates external model when internal model changes
            $scope.$watch('exportModel', function() {
              $scope.updateNgModel();
            }, true);

            // Updates linker when locale changes
            $scope.$watch('locale', function(nv, ov) {
              if (nv === ov || !$scope.linker) {
                return;
              }

              $scope.linker.setKey(nv);
              $scope.linker.update();
            }, true);

            // Updates internal model when external model changes
            $scope.stopWatching = $scope.$watch('ngModel', function(nv, ov) {
              // Find missing categories on initialization only
              if (!ov && nv) {
                $scope.findMissingCategories();
                return;
              }

              $scope.updateExportModel();
            }, true);

            $scope.$on('categorySelector.destroy', $scope.stopWatching);
          },
        };
      }
    ]);
})();
