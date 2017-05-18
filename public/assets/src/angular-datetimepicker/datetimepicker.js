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
            'datetimePicker': '=',
            'datetimePickerMin': '=?',
            'datetimePickerMax': '=?'
          },
          link: function ($scope, element, $attrs) {
            var format = 'YYYY-MM-DD HH:mm:ss';

            if ($attrs.datetimePickerFormat) {
              format = $attrs.datetimePickerFormat;
            }

            if (!$scope.datetimePickerMin) {
              $scope.datetimePickerMin = $window.moment(new Date()).format(format);
            }

            element.datetimepicker({
              useCurrent: false,
              format: format,
              minDate: $scope.datetimePickerMin
            });

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

            // Update min/max values
            $scope.$watch('datetimePickerMin', function (nv) {
              if (moment(nv, format, true).isValid() && nv < $scope.datetimePickerMax) {
                element.data("DateTimePicker").minDate(nv);
              }
            }, true);

            $scope.$watch('datetimePickerMax', function (nv) {
              if (moment(nv, format, true).isValid() && nv > $scope.datetimePickerMin) {
                element.data("DateTimePicker").maxDate(nv);
              }
            }, true);
          }
        };
      }
    ]);
})();
