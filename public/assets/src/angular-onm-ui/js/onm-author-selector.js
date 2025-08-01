(function() {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.ui.authorSelector
   *
   * @requires onm.http
   * @requires onm.localize
   * @requires ui.select
   *
   * @description
   *   The `onm.ui.authorSelector` module provides a directive to
   *   automagically generate an author selector.
   */
  angular.module('onm.ui.authorSelector', [ 'onm.http', 'ui.select' ])

    /**
     * @ngdoc directive
     * @name  onmAuthorSelector
     *
     * @requires http
     *
     * @description
     *   Directive to create category selector dynamically.
     */
    .directive('onmAuthorSelector', [
      'http',
      function(http) {
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
            name: '@',
            ngModel: '=',
            placeholder: '@',
            selected: '=?',
            selectedText: '@'
          },
          template: function() {
            return '<ui-select class="[% cssClass %]" ng-required="required" ng-if="!multiple" ng-model="$parent.$parent.exportModel" theme="select2">' +
              '<ui-select-match placeholder="[% $parent.placeholder %]">' +
              '  <strong ng-if="labelText">[% labelText %]: </strong>[% $select.selected.name %]' +
              '</ui-select-match>' +
              '<ui-select-choices repeat="item in (authors | filter: { name: $select.search })">' +
              '  <div ng-bind-html="item.name | highlight: $select.search"></div>' +
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
                  '<li class="ui-select-choices-row" ng-repeat="item in authors" ng-class="{ \'select2-highlighted\': isSelected(item) }" ng-click="toggle(item)">' +
                    '<div class="select2-result-label ui-select-choices-row-inner">' +
                      '[% item.name %]' +
                    '</div>' +
                  '</li>' +
                '</ul>' +
              '</div>' +
            '</div>';
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

            http.get('api_v1_backend_author_get_list').then(function(response) {
              response.data.items = response.data.items.filter(function(e) {
                return !$scope.exclude || $scope.exclude.length === 0 ||
                  $scope.exclude.indexOf(e.id) === -1;
              });

              if (!$scope.multiple && angular.isArray(response.data.items) &&
                  response.data.items.length > 0 &&
                  response.data.items[0].id !== null) {
                response.data.items.unshift({
                  id: null,
                  name: $scope.defaultValueText
                });
              }

              $scope.authors = response.data.items;
            });

            // Updates the selected item when model or authors change
            $scope.$watch('[ authors, ngModel ]', function() {
              if (!$scope.ngModel || !$scope.authors) {
                $scope.selected = null;
                return;
              }

              if ($scope.multiple && $scope.ngModel &&
                !angular.isArray($scope.ngModel)) {
                $scope.ngModel = [ $scope.ngModel ];
              }

              $scope.selected = $scope.authors.filter(function(e) {
                return e.id === $scope.ngModel;
              });
            }, true);

            /**
             * @function isSelected
             * @memberOf onmAuthorSelector
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
                $scope.ngModel.indexOf(item.id.toString()) !== -1;
            };

            /**
             * @function toggle
             * @memberOf onmAuthorSelector
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
             * @memberOf onmAuthorSelector
             *
             * @description
             *   Adds/removes all items from ngModel.
             */
            $scope.toggleAll = function() {
              if (!$scope.exportModel) {
                $scope.exportModel = [];
              }

              if ($scope.exportModel.length !== $scope.authors.length) {
                $scope.exportModel = angular.copy($scope.authors);
              } else {
                $scope.exportModel = [];
              }
            };

            /**
             * @function updateNgModel
             * @memberOf onmAuthorSelector
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
                  $scope.$parent.form.author) {
                $scope.$parent.form.author.$setDirty(true);
              }

              $scope.ngModel = newValue;
            };

            /**
             * @function updateExportModel
             * @memberOf onmAuthorSelector
             *
             * @description
             *   Updates exportModel basing on current ngModel.
             */
            $scope.updateExportModel = function() {
              if (!$scope.authors) {
                return;
              }

              var needle = $scope.multiple ? [] : [ null ];

              if ($scope.ngModel) {
                needle = $scope.multiple ? $scope.ngModel : [ $scope.ngModel ];
              }

              var found = $scope.authors.filter(function(e) {
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

            // Try to select an option when authors loaded
            $scope.$watch('authors', function(nv) {
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
