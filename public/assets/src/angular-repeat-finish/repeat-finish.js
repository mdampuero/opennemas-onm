(function () {
  'use strict';

  /**
   * @ngdoc directive
   * @name  repeatFinish
   *
   * @requires $timeout
   *
   * @description
   *   Emits an event when the ng-repeat directive finishes rendering.
   */
  angular.module('onm.repeat-finish', [])
    .directive('onRepeatFinish', function ($timeout) {
      return {
        restrict: 'A',
        link: function (scope) {
          if (scope.$last === true) {
            $timeout(function () {
              scope.$emit('ngRepeatFinished');
            });
          }
        }
      };
    });
})();
