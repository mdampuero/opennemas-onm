(function() {
  'use strict';

  angular.module('onm.onmTag', [])

    /**
     * @ngdoc directive
     * @name  onmTag
     *
     * @description
     *   Directive to create forms dynamically.
     */
    .directive('onmTag', [
      '$window',
      function() {
        return {
          restrict: 'E',
          scope: {
            acceptedTags: '=',
            ngModel:      '=',
          },
          template: function() {
            return '<div class="onmTag">' +
                '<div class="acceptedTags">' +
                  '<input class="form-control" ng-model="ngModel" type="text">' +
                '</div>' +
                '<div class="suggestedTags">' +
                '</div>' +
              '</div>';
          }
        };
      }
    ]);
})();

