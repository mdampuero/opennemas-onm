(function() {
  'use strict';
  angular.module('onm.autoformEditor', [])

    /**
     * @ngdoc directive
     * @name  autoform
     *
     * @description
     *   Directive to create forms dynamically.
     */
    .directive('autoformEditor', [
      '$window',
      function() {
        return {
          restrict: 'E',
          scope: {
            extraFields: '=',
            ngModel:     '=',
          },
          template: function() {
            return '<h4 class="no-margin m-b-15">Extra fields</h4>' +
          '<p class="m-b-15">This fields will be added during edition of any article.</p>' +
          '<div class="row" ng-repeat="group in extraFields track by group.group">' +
            '<div class="row">' +
              '<div class="form-group col-md-2">' +
                '<label class="form-label" for="label-[% group.group %]-name">Group internal name</label>' +
                '<div class="controls">[% group.group %]</div>' +
              '</div>' +
              '<div class="form-group col-md-2">' +
                '<label class="form-label" for="label-[% group.group %]-title">Title</label>' +
                '<div class="controls">' +
                  '<input class="form-control" ng-model="group.title" type="text">' +
                '</div>' +
              '</div>' +
            '</div>' +
            '<div class="row">' +
              '<div class="col-md-4">' +
                '<button class="btn btn-danger" ng-click="removeField(group.group)">' +
                  '<i class="fa fa-trash-o"></i>' +
                '</button>' +
              '</div>' +
            '</div>' +
            '<div class="row" ng-repeat="field in group.fields track by field.key">' +
              '<div class="form-group col-md-2">' +
                '<label class="form-label" for="type-[% group.group %]">Fields</label>' +
                '<div class="controls">' +
                  '<select class="form-control" id="type-[% $index %]" ng-model="field.type">' +
                    '<option value="text">{t}Text{/t}</option>' +
                    '<option value="date">{t}Date{/t}</option>' +
                    '<option value="country">{t}Country{/t}</option>' +
                    '<option value="options">{t}Options{/t}</option>' +
                  '</select>' +
                '</div>' +
              '</div>' +
            '</div>' +
/*            '<div class="form-group col-md-6">' +
              '<div class="pull-left">' +
                '<label class="form-label">&nbsp;</label>' +
                '<div class="controls">' +
                  '<button class="btn btn-danger" ng-click="removeField($index)">' +
                    '<i class="fa fa-trash-o"></i>' +
                  '</button>' +
                '</div>' +
              '</div>' +
              '<div class="m-l-15 pull-left" ng-if="field.type === \'options\'">' +
                '<label class="form-label">{t}Options{/t}</label>' +
                '<span class="help">{t}Comma separated list of keys and value (key1:value1, key2:value2,...){/t}</span>' +
                '<div class="controls">' +
                  '<input class="form-control" id="options-[% index %]" ng-model="field.values" type="text">' +
                '</div>' +
              '</div>' +
            '</div>' +*/
          '</div>' +
          '<div class="row">' +
            '<div class="col-md-4">' +
              '<label class="form-label">[% addGroupError %]</label>' +
                '<div class="controls">' +
                  '<input class="form-control" name="groupKey" ng-model="groupKey" type="text" placeholder="Internal name for the new fields group">' +
                  '<button class="btn btn-block btn-success" ng-click="addGroup($event)">' +
                    '<i class="fa fa-plus m-r-5"></i>' +
                    'Add Group' +
                  '</button>' +
                '</div>' +
            '</div>' +
          '</div>';
          },
          link: function($scope) {

            $scope.addGroupError = '';

            /**
             * Change the current language.
             *
             * @param {String} The language value.
             */
            $scope.addGroup = function($event) {
              $event.preventDefault();
              if(!$scope.groupKey || '' === $scope.groupKey) {
                $scope.addGroupError = 'You need add the internal name for the new group';
                return;
              }

              if($scope.groupKey in $scope.extraFields) {
                $scope.addGroupError = 'The internal name ' + $scope.groupKey + ' already exists';
                return;
              }

              $scope.extraFields[$scope.groupKey] = {
                group:  $scope.groupKey,
                title:  '',
                fields: {}
              };
            };
          }
        };
      }
    ]);
})();
