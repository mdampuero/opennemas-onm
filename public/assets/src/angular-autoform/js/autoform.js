(function() {
  'use strict';

  angular.module('onm.autoform', [])

    /**
     * @ngdoc directive
     * @name  autoform
     *
     * @description
     *   Directive to create forms dynamically.
     */
    .directive('autoform', [
      '$window',
      function() {
        return {
          restrict: 'E',
          scope: {
            fieldsByModule: '=',
            ngModel:        '=',
            countries:      '=',
            text:           '@',
          },
          template: function() {
            return '<div ng-repeat="moduleFields in fieldsByModule">' +
              '<div class="form-group" ng-repeat="field in moduleFields.fields">' +
                '<label class="form-label" for="{{ field.key }}">{{ field.title }}</label>' +
                '<div class="controls">' +
                  '<input class="form-control" id="{{ field.key }}" name="{{ field.key }}" ng-if="field.type === \'text\'" ng-model="ngModel[field.key]" type="text">' +
                  '<input class="form-control" datetime-picker id="{{ field.key }}" name="{{ field.key }}" ng-if="field.type === \'date\'" ng-model="ngModel[field.key]" type="text">' +
                  '<select class="form-control" id="{{ field.key }}" name="{{ field.key }}" ng-if="field.type === \'country\'" ng-model="ngModel[field.key]">' +
                    '<option value="">{{ text }}</option>' +
                    '<option value="{{ key }}" ng-repeat="(key,value) in countries" ng-selected="{{ ngModel[field.key] === value }}">{{ value }}</option>' +
                  '</select>' +
                  '<select class="form-control" id="{{ field.key }}" name="{{ field.key }}" ng-if="field.type === \'select\'" ng-model="ngModel[field.key]" ng-change="updateMirror(field)" ng-init="initMirror(field)">' +
                    '<option ng-repeat="option in field.values.split(\',\')" value="{{ option.split(\':\')[0] }}">' +
                      '{{ option.split(\':\')[1] }}' +
                    '</option>' +
                  '</select>' +
                  '<input ng-if="field.type === \'select\'" type="hidden" id="{{ field.key }}_val" name="{{ field.key }}_val" ng-model="ngModel[field.key + \'_val\']" readonly />' +
                  '<div class="radio" ng-if="field.type === \'radio\'" ng-repeat="option in field.values.split(\',\')">' +
                    '<input id="{{ field.key }}-option-{{ option.split(\':\')[0] }}" name="{{ field.key }}" ng-model="ngModel[field.key]" value="{{ option.split(\':\')[0] }}" type="radio">' +
                    '<label for="{{ field.key }}-option-{{ option.split(\':\')[0] }}">{{ option.split(\':\')[1] }}</label>' +
                  '</div>' +
                '</div>' +
              '</div>' +
            '</div>';
          },
          link: function(scope) {
            scope.updateMirror = function(field) {
              var selectedId = scope.ngModel[field.key];
              var options = field.values.split(',');

              for (var i = 0; i < options.length; i++) {
                var parts = options[i].split(':');
                var id = parts[0];
                var name = parts[1];

                if (id === selectedId) {
                  scope.ngModel[field.key + '_val'] = name;
                  break;
                }
              }
            };

            scope.initMirror = function(field) {
              if (scope.ngModel[field.key]) {
                scope.updateMirror(field);
              }
            };
          }
        };
      }
    ]);
})();
