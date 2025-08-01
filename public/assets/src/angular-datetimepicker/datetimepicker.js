(function() {
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
    .directive('datetimePicker', [
      '$timeout', '$window',
      function($timeout, $window) {
        return {
          restrict: 'A',
          scope: {
            ngModel: '=',
            datetimePicker: '=',
            datetimePickerMin: '=?',
            datetimePickerMax: '=?'
          },
          link: function($scope, element, $attrs) {
            var config = {
              format:     $attrs.datetimePickerFormat || 'YYYY-MM-DD HH:mm:ss',
              useCurrent: $attrs.datetimePickerUseCurrent === 'true'
            };

            if ($attrs.datetimePickerTimezone) {
              $window.moment.tz.setDefault($attrs.datetimePickerTimezone);
            }

            if ($attrs.datetimePickerMin && $scope.datetimePickerMin) {
              config.minDate = $scope.datetimePickerMin;
            }

            if ($attrs.datetimePickerMax && $scope.datetimePickerMax) {
              config.maxDate = $scope.datetimePickerMax;
            }

            var htmlEle = document.getElementsByTagName('HTML');

            if (
              htmlEle &&
              htmlEle.length === 1 &&
              htmlEle[0].attributes &&
              htmlEle[0].attributes.length > 0
            ) {
              var attributes = htmlEle[0].attributes;

              for (var i = 0; i < attributes.length; i++) {
                if (attributes[i].name === 'lang') {
                  config.locale = moment.locales().includes(attributes[i].value) ?
                    attributes[i].value :
                    'en';
                  break;
                }
              }
            }

            element.datetimepicker(config);

            var picker = element.data('DateTimePicker');

            if ($attrs.datetimePicker) {
              $scope.datetimePicker = picker;
            }

            if ($scope.ngModel) {
              $scope.ngModel = $window.moment($scope.ngModel)
                .format(config.format);
            }

            element.on('dp.change', function() {
              $scope.ngModel = null;

              if (picker.date()) {
                $timeout(function() {
                  var date = $window.moment(picker.date());

                  $scope.ngModel = date.format(config.format);
                });
              }
            });

            $scope.$watch('datetimePickerMin', function(nv) {
              if ($window.moment(nv, config.format, true).isValid() &&
                  $attrs.datetimePickerMin) {
                element.data('DateTimePicker').minDate(nv);
              }
            }, true);

            $scope.$watch('datetimePickerMax', function(nv) {
              if ($window.moment(nv, config.format, true).isValid() &&
                  $attrs.datetimePickerMax) {
                element.data('DateTimePicker').maxDate(nv);
              }
            }, true);
          }
        };
      }
    ]);
})();
