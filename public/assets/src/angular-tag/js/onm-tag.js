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
            locale:                '=',
            tagsList:              '=',
            ngModel:               '=',
            checkNewTags:          '=',
            getSuggestedTags:      '=',
            loadAutoSuggestedTags: '=',
            suggestedTags:         '=',
            placeholder:           '@',
          },
          template: function() {
            return '<a class="reload-tags btn btn-primary btn-xs pull-right" ng-click="loadAutoSuggestedTags()" href="#">' +
                '<i class="fa fa-refresh m-r-5"></i>' +
                window.tagTranlations.reload +
              '</a>' +
              '<div class="onmTag">' +
                '<div class="currentTags">' +
                  '<ul class="onmTagList">' +
                    '<li ng-repeat="acceptedTag in acceptedTags()" ng-class="getTagCssClass(acceptedTag)">' +
                      '<span>[% acceptedTag.name %]</span>' +
                      '<a href="#" ng-click="removeTag(acceptedTag)"><i class="fa fa-times tagIcon" aria-hidden="true"></i></a>' +
                    '</li>' +
                  '</ul>' +
                  '<div class="tag-input">' +
                    '<input type="text" ng-class=" invalidTag ? \'has-error \' : \'\'" ng-model="newTag" placeholder="[% placeholder %]" ng-keydown="inputTagKeyUp($event)" uib-typeahead="tagSuggested as tagSuggested.name for tagSuggested in getLocalSuggestedTags($viewValue)" typeahead-loading="isSuggesting" typeahead-wait-ms="500" typeahead-on-select="suggestedTagAccepted($item, $model, $label)" typeahead-min-length="2" typeahead-focus-first="false">' +
                    '<i class="tag-input-icon fa fa-circle-o-notch fa-spin loading-icon" ng-show="isSuggesting || isValidating"></i>' +
                  '</div>' +
                  '<input type="hidden" name="tag_ids" ng-value="getTagIdsList()">' +
                '</div>' +
                '<div class="autoSuggested" ng-show="loadAutoSuggestedTags">' +
                  '<span class="title">' + window.tagTranlations.suggestedTag + '</span>' +
                  '<ul class="onmTagList">' +
                    '<li ng-repeat="suggestedTag in suggestedTags" class="newTag">' +
                      '<span>[% suggestedTag.name %]</span><a href="#" ng-click="addSuggestedTag(suggestedTag)"><i class="fa fa-plus tagIcon" aria-hidden="true"></a>' +
                    '</li>' +
                  '</ul>' +
                '</div>' +
              '</div>';
          },
          link: function($scope) {
            if (!$scope.placeholder) {
              $scope.placeholder = '';
            }

            $scope.newTag       = '';
            $scope.invalidTag   = false;
            $scope.isValidating = false;
            $scope.tagToDelete  = null;

            /**
             * @function acceptedTags
             * @memberOf onm-tag
             *
             * @description
             *   Return the tags for the current locale
             *
             * @return {Array} List of tags for the current locale
             */
            $scope.acceptedTags = function() {
              var returnVal = [];

              if (!$scope.ngModel) {
                return returnVal;
              }

              for (var i = 0; i < $scope.ngModel.length; i++) {
                if (
                  typeof $scope.ngModel[i] === 'object' &&
                  $scope.ngModel[i].language_id === $scope.locale
                ) {
                  returnVal.push($scope.ngModel[i]);
                } else if (
                  $scope.ngModel[i] in $scope.tagsList &&
                  $scope.tagsList[$scope.ngModel[i]].language_id === $scope.locale
                ) {
                  returnVal.push($scope.tagsList[$scope.ngModel[i]]);
                }
              }
              return returnVal;
            };

            /**
             * @function inputTagKeyUp
             * @memberOf onm-tag
             *
             * @description
             *   Method that captures the key up event from the tag input and
             * triggers the corresponding action. (create or delete a tag).
             *
             * @param {event} e - keyup event
             */
            $scope.inputTagKeyUp = function(e) {
              $scope.invalidTag = false;

              // Check if the action is delete a tag 8 - backspace
              if (e.keyCode === 8 && $scope.newTag === '') {
                e.preventDefault();
                if ($scope.tagToDelete !== null) {
                  $scope.removeTag($scope.tagToDelete);
                  $scope.tagToDelete = null;
                } else {
                  var tagToDelete = $scope.ngModel[$scope.ngModel.length - 1];

                  if (typeof tagToDelete === 'object') {
                    $scope.tagToDelete = tagToDelete;
                  } else {
                    $scope.tagToDelete = $scope.tagsList[tagToDelete];
                  }
                }
                return null;
              }
              $scope.tagToDelete = null;

              // If is other than add a new tag 13 - enter, 188 - comma, 9 - tab
              if ([ 13, 188, 9 ].indexOf(e.keyCode) === -1) {
                return null;
              }

              e.preventDefault();

              // Add new tag
              $scope.addTag();
              return null;
            };

            /**
             * @function getLocalSuggestedTags
             * @memberOf onm-tag
             *
             * @description
             *   Return the suggested tags for the current locale.
             *
             * @param String   - Field from we retreave the suggested tags
             *
             * @return {Array} List of tags for the current locale
             */
            $scope.getLocalSuggestedTags = function(tagName) {
              var suggestedTags = $scope.getSuggestedTags($scope.locale, tagName, $scope.ngModel);

              return suggestedTags;
            };

            /**
             * @function suggestedTagAccepted
             * @memberOf onm-tag
             *
             * @description
             *   Return the suggested tags for the current locale.
             *
             * @param String  $item - Add a new tag to the list of tags
             */
            $scope.suggestedTagAccepted = function($item) {
              if (!($item.id in $scope.tagsList)) {
                $scope.tagsList[$item.id] = $item;
              }
              $scope.ngModel.push($item.id);
              $scope.newTag = '';
              return null;
            };

            /**
             * @function removeTag
             * @memberOf onm-tag
             *
             * @description
             *   Remove some tag from the tag list.
             *
             * @param {Object}  tag2delete - Tag to delete
             */
            $scope.removeTag = function(tag2delete) {
              for (var i = 0; i < $scope.ngModel.length; i++) {
                // If delete tag and array position tag are new check if are the same
                if (
                  !tag2delete.id &&
                  typeof $scope.ngModel[i] === 'object' &&
                  tag2delete.language_id === $scope.ngModel[i].language_id &&
                  tag2delete.name === $scope.ngModel[i].name
                ) {
                  $scope.ngModel.splice(i, 1);
                  return null;
                // If delete tag and array position tag have id, check if the ids are the same
                }

                if (
                  tag2delete.id &&
                  typeof $scope.ngModel[i] === 'number' &&
                  tag2delete.id === parseInt($scope.ngModel[i])
                ) {
                  $scope.ngModel.splice(i, 1);
                  return null;
                }
              }
              return null;
            };

            /**
             * @function addSuggestedTag
             * @memberOf onm-tag
             *
             * @description
             *   Add some tag from the suggested list.
             *
             * @param {Object}  newTag - Tag to add
             */
            $scope.addSuggestedTag = function(newTag) {
              for (var i = 0; i < $scope.suggestedTags.length; i++) {
                // If delete tag and array position tag are new check if are the same
                if (
                  newTag.language_id === $scope.suggestedTags[i].language_id &&
                  newTag.name === $scope.suggestedTags[i].name
                ) {
                  $scope.suggestedTags.splice(i, 1);
                  $scope.ngModel.push(newTag);
                  return null;
                // If delete tag and array position tag have id, check if the ids are the same
                }
              }
              return null;
            };

            /**
             * @function addTag
             * @memberOf onm-tag
             *
             * @description
             *   Add some tag from the input field.
             */
            $scope.addTag = function() {
              if ($scope.newTag === '') {
                $scope.invalidTag = false;
                return null;
              }

              for (var i = 0; i < $scope.ngModel.length; i++) {
                if (!$scope.ngModel[i].id && $scope.ngModel[i].name === $scope.newTag) {
                  $scope.invalidTag = true;
                  return null;
                }
                if (Number.isInteger($scope.ngModel[i]) &&
                  $scope.tagsList[$scope.ngModel[i]].language_id === $scope.locale &&
                  $scope.tagsList[$scope.ngModel[i]].name === $scope.newTag
                ) {
                  $scope.invalidTag = true;
                  return null;
                }
              }

              var callback = function(response) {
                if (!response.data || !response.data.items ||
                  !Array.isArray(response.data.items) ||
                  response.data.items.length !== 1
                ) {
                  $scope.invalidTag = true;
                } else {
                  $scope.invalidTag = false;
                  var tag = response.data.items[0];

                  if (tag.id) {
                    $scope.tagsList[tag.id] = tag;
                    $scope.ngModel.push(tag.id);
                  } else {
                    $scope.ngModel.push(tag);
                    $scope.removeFromSuggested(tag);
                  }
                  $scope.newTag = '';
                }
                $scope.isValidating = false;
                $scope.isSuggesting = false;
              };

              $scope.isValidating = true;
              this.checkNewTags([ $scope.newTag ], $scope.locale, callback);
              return null;
            };

            /**
             * @function removeFromSuggested
             * @memberOf onm-tag
             *
             * @description
             *   Remove some tag from the tag list of suggested.
             *
             * @param {Object}  tag - Tag to delete
             */
            $scope.removeFromSuggested = function(tag) {
              if (!$scope.suggestedTags) {
                return null;
              }
              for (var i = 0; i < $scope.suggestedTags.length; i++) {
                // If delete tag and array position tag are new check if are the same
                if (
                  tag.language_id === $scope.suggestedTags[i].language_id &&
                  tag.name === $scope.suggestedTags[i].name
                ) {
                  $scope.suggestedTags.splice(i, 1);
                  return null;
                // If delete tag and array position tag have id, check if the ids are the same
                }
              }
              return null;
            };

            /**
             * @function getTagIdsList
             * @memberOf onm-tag
             *
             * @description
             *   Retrieve the list of tags as json
             */
            $scope.getTagIdsList = function() {
              return JSON.stringify($scope.ngModel);
            };

            /**
             * @function getTagCssClass
             * @memberOf onm-tag
             *
             * @description
             *  Get the value for the css tag
             *
             * @param tag
             *
             * @return The css value for the tag
             */
            $scope.getTagCssClass = function(tag) {
              if ($scope.tagToDelete === null) {
                return !tag.id ? 'newTag' : '';
              }

              if (!tag.id && tag.name !== $scope.tagToDelete.name) {
                return 'newTag';
              }

              if (tag.id && tag.id !== $scope.tagToDelete.id) {
                return '';
              }

              return 'toDeleteTag';
            };
          },
        };
      }
    ]);
})();

