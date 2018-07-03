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
            ngModel: '=',
          },
          template: function() {
            return '<h4 class="no-margin m-b-15">Extra fields</h4>' +
          '<p class="m-b-15">This fields will be added during edition of any article.</p>' +
          '<p class="m-b-15">' +
            '<span ng-repeat="error in autoformEditorErrors">[% error %]</span>' +
          '</p>' +
          '<div ng-repeat="(groupIndex, group) in ngModel">' +
            '<div class="row">' +
              '<div class="form-group col-md-3">' +
                '<label class="form-label" for="label-[% group.group %]-name">Group internal name</label>' +
                '<div class="controls">[% group.group %]</div>' +
              '</div>' +
              '<div class="form-group col-md-8">' +
                '<label class="form-label" for="label-[% group.group %]-title">Title</label>' +
                '<div class="controls">' +
                  '<input class="form-control" ng-model="group.title" type="text">' +
                '</div>' +
              '</div>' +
            '</div>' +
            '<div class="row" ng-repeat="field in group.fields">' +
              '<div class="form-group col-md-3">' +
                '<label class="form-label p-l-15" for="label-[% field.key %]-name">Internal name</label>' +
                '<div class="controls p-l-15">' +
                  '[% field.key %]' +
                '</div>' +
              '</div>' +
              '<div class="form-group col-md-4">' +
                '<label class="form-label" for="label-[% field.key %]-title">Name</label>' +
                '<div class="controls">' +
                  '<input class="form-control" ng-model="field.title" type="text">' +
                '</div>' +
              '</div>' +
              '<div class="form-group col-md-4">' +
                '<label class="form-label" for="type-[% field.key %]">Type</label>' +
                '<div class="controls">' +
                  '<select class="form-control" id="type-[% field.key %]" ng-model="field.type">' +
                    '<option value="text">Text</option>' +
                    '<option value="date">Date</option>' +
                    '<option value="country">Country</option>' +
                    '<option value="radio">Radio</option>' +
                    '<option value="select">Select</option>' +
                  '</select>' +
                  '<div class="m-t-15" ng-if="field.type === \'radio\' || field.type === \'select\'">' +
                    '<label class="form-label">Options</label>' +
                    '<span class="help m-l-5">Comma separated list of keys and value (key1:value1, key2:value2,...)</span>' +
                    '<div class="controls">' +
                      '<input class="form-control" id="options-[% field.key %]" ng-model="field.values" type="text">' +
                    '</div>' +
                  '</div>' +
                '</div>' +
              '</div>' +
              '<div class="form-group col-md-1">' +
                '<div class="pull-left">' +
                  '<label class="form-label">&nbsp;</label>' +
                  '<div class="controls">' +
                    '<button class="btn btn-danger" ng-click="removeField(group.group, field.key, $event)">' +
                      '<i class="fa fa-trash-o"></i>' +
                    '</button>' +
                  '</div>' +
                '</div>' +
              '</div>' +
            '</div>' +
            '<div class="row">' +
              '<div class="col-md-12">' +
                '<label class="form-label p-l-15">Internal name</label>' +
                '<div class="controls row p-l-15">' +
                  '<div class="col-md-7">' +
                    '<input class="form-control" name="fieldKeys" ng-model="fieldKeys[group.group]" type="text" placeholder="Internal name for the new fields">' +
                  '</div>' +
                  '<div class="col-md-4">' +
                    '<button class="btn btn-block btn-success" ng-click="addField(group.group, $event)">' +
                      '<i class="fa fa-plus m-r-5"></i>' +
                      'Add Field' +
                    '</button>' +
                  '</div>' +
                '</div>' +
                '<div class="help text-danger p-l-15 p-t-5">[% addFieldErrors[group.group] ? addFieldErrors[group.group] : \'&nbsp;\' %]</div>' +
              '</div>' +
            '</div>' +
            '<div class="row p-b-50 p-t-5">' +
              '<div class="col-md-11">' +
                '<button class="btn btn-block btn-danger" ng-click="removeGroup(group.group, $event)" type="button">' +
                  '<i class="fa fa-trash-o"></i> Delete Group' +
                '</button>' +
              '</div>' +
            '</div>' +
          '</div>' +
          '<div class="row">' +
            '<div class="col-md-7">' +
              '<div class="row">' +
                '<div class="col-md-6">' +
                  '<label class="form-label">Group internal name</label>' +
                  '<div class="controls">' +
                    '<input class="form-control" name="groupKey" ng-model="groupKey" type="text" placeholder="Internal name for the new fields group">' +
                  '</div>' +
                '</div>' +
                '<div class="col-md-6">' +
                  '<label class="form-label">&nbsp;</label>' +
                  '<button class="btn btn-block btn-success" ng-click="addGroup($event)">' +
                    '<i class="fa fa-plus m-r-5"></i>' +
                    'Add Group' +
                  '</button>' +
                '</div>' +
              '</div>' +
              '<div class="row">' +
                '<div class="help text-danger p-t-15">[% addGroupError ? addGroupError : \'&nbsp;\' %]</div>' +
              '</div>' +
          '</div>';
          },
          link: function($scope) {
            $scope.addGroupError        = '';
            $scope.addFieldErrors       = {};
            $scope.groupKey             = '';
            $scope.fieldKeys            = {};
            $scope.autoformEditorErrors = '';

            var underscore = function(str) {
              return str.split(' ').join('_').toLowerCase();
            };

            if (!$scope.ngModel || Array.isArray($scope.ngModel) && $scope.ngModel.length === 0) {
              $scope.ngModel = {};
            }

            /**
             * Change the current language.
             *
             * @param {Object} $event The language value.
             */
            $scope.addGroup = function($event) {
              $event.preventDefault();
              if (!$scope.groupKey || $scope.groupKey === '') {
                $scope.addGroupError = 'You need to add an identifier for the group to be added';
                return;
              }

              var groupKey = underscore($scope.groupKey);

              if (groupKey in $scope.ngModel) {
                $scope.addGroupError = 'The identifier for the group \'' + $scope.groupKey + '\' already exists';
                return;
              }

              $scope.ngModel[groupKey] = {
                group:  groupKey,
                title:  '',
                fields: {}
              };

              $scope.groupKey                 = '';
              $scope.fieldKeys[groupKey]      = '';
              $scope.addFieldErrors[groupKey] = '';
              $scope.addGroupError            = '';
            };

            /**
             * Remove group
             *
             * @param {String} group  The language value.
             * @param {Object} $event Language value.
             */
            $scope.removeGroup = function(group) {
              if (!(group in $scope.ngModel)) {
                return;
              }
              delete $scope.ngModel[group];
              delete $scope.fieldKeys[group];
              delete $scope.addFieldErrors[group];
            };

            /**
             * Add a group field
             *
             *  @param {String} group Name for the group where add the field
             */
            $scope.addField = function(group, $event) {
              $event.preventDefault();
              if (!$scope.fieldKeys[group] || $scope.fieldKeys[group] === '') {
                $scope.addFieldErrors[group] = 'You need to add an identifier for the field to be added';
                return;
              }

              var fieldKey = underscore($scope.fieldKeys[group]);

              if (!(group in $scope.ngModel) ||
                $scope.ngModel[group].fields !== '' &&
                fieldKey in $scope.ngModel[group].fields
              ) {
                $scope.addFieldErrors[group] =
                  'The identifier for the field \'' +
                  $scope.fieldKeys[group] +
                  '\' already exists in the group ' + group;
                return;
              }

              if ($scope.ngModel[group].fields === '' ||
                Array.isArray($scope.ngModel[group].fields) &&
                $scope.ngModel[group].fields.length === 0
              ) {
                $scope.ngModel[group].fields = {};
              }

              $scope.ngModel[group].fields[fieldKey] = {
                title:  '',
                type:  '',
                key:   fieldKey
              };

              $scope.fieldKeys[group]      = '';
              $scope.addFieldErrors[group] = '';
            };

            /**
             *  Remove a group field
             *
             * @param {String} group internal name for the group where remove the field
             * @param {String} field internal name to remove
             */
            $scope.removeField = function(group, field, $event) {
              $event.preventDefault();
              if (!(group in $scope.ngModel) || !(field in $scope.ngModel[group].fields)) {
                return;
              }
              delete $scope.ngModel[group].fields[field];
            };
          }
        };
      }
    ]);
})();
