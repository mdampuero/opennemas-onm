(function () {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.resizable
   *
   * @description
   *   The `onm.resizable` module provides a directive to expose and update
   *   window dimensions when window is resized.
   */
  angular.module('onm.resizable', [])
    /**
     * @ngdoc directive
     * @name  resizable
     *
     * @requires $window
     * @requires $scope
     *
     * @description
     *   Directive to expose and update the current window dimensions.
     *
     * ###### Attributes:
     * - **`resizable`**: Initializes the directive. (Required)
     *
     * @example
     * <!-- Initializes the directive -->
     * <body resizable>
     *   <!-- Content -->
     * </body>
     */
    .directive('resizable', ['$window',
      function($window) {
        return {
          restrict: 'A',
          link: function($scope) {
            $scope.windowHeight = $window.innerHeight;
            $scope.windowWidth  = $window.innerWidth;

            angular.element($window).bind('resize', function() {
              $scope.windowHeight = $window.innerHeight;
              $scope.windowWidth  = $window.innerWidth;

              $scope.$apply();
            });
          }
        };
      }
    ]);
})();
