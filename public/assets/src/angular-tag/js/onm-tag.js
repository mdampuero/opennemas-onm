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
            return '<div class="onmTag">' +
                '<div class="currentTags">' +
                  '<ul class="onmTagList">' +
                    '<li ng-repeat="acceptedTag in acceptedTags()" ng-class=" !acceptedTag.id ? \'newTag\' : \'\'">' +
                      '<span>[% acceptedTag.name %]</span>' +
                      '<a href="#" ng-click="removeTag(acceptedTag)"><i class="fa fa-times tagIcon" aria-hidden="true"></i></a>' +
                    '</li>' +
                  '</ul>' +
                  '<div class="tag-input">' +
                    '<input type="text" ng-class=" invalidTag ? \'has-error \' : \'\'" ng-model="newTag" placeholder="[% placeholder %]" ng-keydown="validateTag($event)" uib-typeahead="tagSuggested as tagSuggested.name for tagSuggested in getLocalSuggestedTags($viewValue)" typeahead-loading="isSuggesting" typeahead-wait-ms="500" typeahead-on-select="suggestedTagAccepted($item, $model, $label)" typeahead-min-length="2" typeahead-focus-first="false">' +
                    '<i class="fa fa-circle-o-notch fa-spin loading-icon" ng-show="isSuggesting || isValidating"></i>' +
                  '</div>' +
                  '<input type="hidden" name="tag_ids" ng-value="getTagIdsList()">' +
                '</div>' +
                '<div class="autoSuggested" ng-show="loadAutoSuggestedTags">' +
                  '<span class="title">' + window.tagTranlations.suggestedTag + '</span>' +
                  '<a class="reload-tags btn btn-primary btn-xs pull-right" ng-click="loadAutoSuggestedTags()" href="#">' +
                    '<i class="fa fa-refresh m-r-5"></i>' +
                    window.tagTranlations.reload +
                  '</a>' +
                  '<ul class="onmTagList">' +
                    '<li ng-repeat="suggestedTag in suggestedTags" class="newTag">' +
                      '<span>[% suggestedTag.name %]</span><a href="#" ng-click="addTag(suggestedTag)"><i class="fa fa-plus tagIcon" aria-hidden="true"></a>' +
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
             * @function validateTag
             * @memberOf onm-tag
             *
             * @description
             *   Add a new tag to the list when the user press enter.
             */
            $scope.validateTag = function(e) {
              $scope.invalidTag = false;
              if (e.keyCode !== 13) {
                return null;
              }
              e.preventDefault();
              if ($scope.newTag === '') {
                $scope.invalidTag = false;
                return null;
              }

              for (var i = 0; i < $scope.ngModel.length; i++) {
                if (!$scope.ngModel[i].id && $scope.ngModel[i].name === $scope.newTag) {
                  $scope.invalidTag = true;
                  return null;
                }
                if ($scope.ngModel[i].id &&
                  $scope.tagsList[$scope.ngModel[i].id].locale === $scope.locale &&
                  $scope.tagsList[$scope.ngModel[i].id].locale === $scope.newTag
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
             * @function addTag
             * @memberOf onm-tag
             *
             * @description
             *   Remove some tag from the tag list.
             *
             * @param {Object}  tag2delete - Tag to delete
             */
            $scope.addTag = function(newTag) {
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
          },
        };
      }
    ]);
})();

