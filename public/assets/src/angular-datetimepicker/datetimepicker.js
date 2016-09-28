(function () {
  'use strict';

  angular.module('onm.datetimepicker', [])
    /**
     * @ngdoc directive
     * @name  datetime-picker
     *
     * @requires $timeout
     * @requires $window
     *
     * @description
     *   Directive to create a datetime picker.
     *
     *  ###### Attributes:
     *  - **`datetimepicker`**: Initializes the directive. Attribute value could be a valid format.
     *
     * @example
     * <!-- Datetime picker without format -->
     * <input datetime-picker ng-model="date">
     *
     * @example
     * <!-- Datetime picker with format -->
     * <input datetime-picker="YY-MM-DD" ng-model="date">
     */
    .directive('datetimePicker', [ '$timeout', '$window',
      function ($timeout, $window) {
        return {
          restrict: 'A',
          scope: {
            'ngModel': '=',
            'datetimePicker': '='
          },
          link: function ($scope, element, $attrs) {
            var format = 'YYYY-MM-DD HH:mm:ss';

            if ($attrs.datetimePickerFormat) {
              format = $attrs.datetimePickerFormat;
            }

            element.datetimepicker({ useCurrent: false, format: format });

            var picker = element.data('DateTimePicker');

            if ($attrs.datetimePicker) {
              $scope.datetimePicker = picker;
            }

            element.on('dp.change', function() {
                $scope.ngModel = null;

                if (picker.date()) {
                  $timeout(function() {
                    var date = $window.moment(picker.date());
                    $scope.ngModel = date.format(format);
                  });
                }
            });
          }
        };
      }
    ]);
})();
