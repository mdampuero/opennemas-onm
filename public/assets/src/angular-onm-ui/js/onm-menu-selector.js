(function() {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.ui.menuSelector
   *
   * @requires onm.http
   * @requires ui.select
   *
   * @description
   *   The `onm.ui.menuSelector` module provides a directive to automagically
   *   generate a menu selector.
   */
  angular.module('onm.ui.menuSelector', [ 'onm.http', 'ui.select' ])

    /**
     * @ngdoc directive
     * @name  onmCategorySelector
     *
     * @requires http
     *
     * @description
     *   Directive to create menu selector dynamically.
     */
    .directive('onmMenuSelector', [
      'http',
      function(http) {
        return {
          restrict: 'E',
          transclude: true,
          scope: {
            defaultValueText: '@',
            labelText: '@',
            ngModel: '=',
            placeholder: '@',
            selected: '=?',
            selectedText: '@'
          },
          template: function() {
            return '<ui-select class="[% cssClass %]" ng-required="[% required %]" name="menu" ng-model="$parent.ngModel" theme="select2">' +
              '<ui-select-match placeholder="[% $parent.placeholder %]">' +
              '  <strong ng-if="labelText">[% labelText %]: </strong>[% $select.selected.name %]' +
              '</ui-select-match>' +
              '<ui-select-choices repeat="item.pk_menu as item in (menus | filter: { name: $select.search })">' +
              '  <div ng-bind-html="item.name | highlight: $select.search"></div>' +
              '</ui-select-choices>' +
            '</ui-select>';
          },
          link: function($scope, elem, $attrs) {
            $scope.cssClass     = $attrs.class ? $attrs.class : '';
            $scope.required     = $attrs.required ? $attrs.required : false;
            $scope.selectedText = $scope.selectedText || 'selected';

            // Force integers in ngModel on initialization
            if ($scope.ngModel) {
              $scope.ngModel = parseInt($scope.ngModel);
            }

            http.get('backend_ws_menus_list').then(function(response) {
              if (angular.isArray(response.data.results) &&
                  response.data.results.length > 0 &&
                  response.data.results[0].pk_menu !== null) {
                response.data.results.unshift({
                  pk_menu: null,
                  name: $scope.defaultValueText
                });
              }

              $scope.menus = response.data.results;
            });
          },
        };
      }
    ]);
})();
