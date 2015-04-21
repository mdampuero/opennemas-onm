(function () {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.scroll
   *
   * @requires require
   *
   * @description
   *   The `onm.scroll` module provides a directive to execute actions on
   *   element scroll.
   */
  angular.module('onm.scroll', [])
    /**
     * @ngdoc directive
     * @name  whenScrolled
     *
     * @description
     *   Directive to execute actions on element scroll.
     *
     * ###### Attributes:
     * - **`when-scrolled`**: The callback to execute on scroll. (Required)
     *
     * @example
     * <!-- Initializes the directive -->
     * <div when-scrolled="callback()">
     *   <!-- Content -->
     * </div>
     */
    .directive('whenScrolled', [
      function() {
        return {
          restrict: 'A',
          link: function($scope, $element, $attrs) {
            var raw = $element[0];

            $element.bind('scroll', function() {
              if (raw.scrollTop > 0 &&
                  raw.scrollTop + raw.offsetHeight >= raw.scrollHeight - 1) {
                $scope.$apply($attrs.whenScrolled);
              }
            });
          }
        };
      }
    ]);
})();
