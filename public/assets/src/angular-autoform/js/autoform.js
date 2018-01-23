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
          },
          template: function() {
            return '<div class="col-md-12" ng-repeat="moduleFields in fieldsByModule">' +
              '<h5>{{ moduleFields.title }}</h5>' +
              '<div class="form-group" ng-repeat="field in moduleFields.fields">' +
                '<label class="form-label" for="{{ field.key }}">{{ field.name }}</label>' +
                '<div class="controls">' +
                  '<input class="form-control" id="{{ field.key }}" name="{{ field.key }}" ng-if="field.type === \'text\'" ng-model="ngModel[field.key]" type="text">' +
                  '<input class="form-control" datetime-picker id="{{ field.key }}" name="{{ field.key }}" ng-if="field.type === \'date\'" ng-model="ngModel[field.key]" type="text">' +
                  '<select class="form-control" id="{{ field.key }}" name="{{ field.key }}" ng-if="field.type === \'country\'" ng-model="ngModel[field.key]">' +
                    '<option value="">{t}Select a country{/t}...</option>' +
                    '<option value="{{ key }}" ng-repeat="(key,value) in extra.countries" ng-selected="{{ ngModel[field.key] === value }}">{{ value }}</option>' +
                  '</select>' +
                  '<div class="radio" ng-if="field.type === \'options\'" ng-repeat="option in field.values">' +
                    '<input id="option-{{ option.key }}" name="{{ field.key }}" ng-model="ngModel[field.key]" value="{{ option.key }}" type="radio">' +
                    '<label for="option-{{ option.key }}">{{ option.value }}</label>' +
                  '</div>' +
                '</div>' +
              '</div>' +
            '</div>';
          }
        };
      }
    ]);
})();
