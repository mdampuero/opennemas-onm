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
            var useCurrent = false;

            if ($attrs.datetimePickerFormat) {
              format = $attrs.datetimePickerFormat;
            }

            if ($attrs.datetimePickerUseCurrent) {
              useCurrent = JSON.parse($attrs.datetimePickerUseCurrent);
            }

            var config = {
              format: format,
              useCurrent: useCurrent
            };

            if ($attrs.datetimePickerMin && $scope.datetimePickerMin) {
              config.minDate = $scope.datetimePickerMin;
            }

            if ($attrs.datetimePickerMax && $scope.datetimePickerMax) {
              config.maxDate = $scope.datetimePickerMax;
            }

            element.datetimepicker(config);

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
              if ($window.moment(nv, format, true).isValid() &&
                  $attrs.datetimePickerMin) {
                element.data("DateTimePicker").minDate(nv);
              }
            }, true);

            $scope.$watch('datetimePickerMax', function (nv) {
              if ($window.moment(nv, format, true).isValid() &&
                  $attrs.datetimePickerMax) {
                element.data("DateTimePicker").maxDate(nv);
              }
            }, true);
          }
        };
      }
    ]);
})();
