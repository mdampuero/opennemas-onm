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
            placeholder:           '@'
          },
          template: function() {
            return '<div class="onmTag">' +
                '<div>' +
                  '<ul class="onmTagList">' +
                    '<li ng-repeat="acceptedTag in acceptedTags()" ng-class=" !acceptedTag.id ? \'newTag\' : \'\'">' +
                      '<span>[% acceptedTag.name %]</span><a href="#" ng-click="removeTag(acceptedTag)">x</a>' +
                    '</li>' +
                  '</ul>' +
                  '<input type="text" ng-class=" invalidTag ? \'has-error \' : \'\'" ng-model="newTag" placeholder="[% placeholder %]" ng-keydown="validateTag($event)" uib-typeahead="tagSuggested as tagSuggested.name for tagSuggested in getLocalSuggestedTags($viewValue)" typeahead-loading="loadingLocations" typeahead-wait-ms="500" typeahead-on-select="suggestedTagAccepted($item, $model, $label)" typeahead-min-length="2" typeahead-focus-first="false">' +
                  '<input type="hidden" name="tag_ids" ng-value="getTagIdsList()">' +
                '</div>' +
                '<div ng-if="loadAutoSuggestedTags">' +
                  '<span>Suggested tags</span>' +
                  '<a class="btn btn-primary btn-xs" ng-click="loadAutoSuggestedTags()" href="#">' +
                    '<i class="fa fa-refresh m-r-5"></i>' +
                    'Reload' +
                  '</a>' +
                  '<ul class="onmTagList">' +
                    '<li ng-repeat="suggestedTag in suggestedTags" class="newTag">' +
                      '<span>[% suggestedTag.name %]</span><a href="#" ng-click="addTag(suggestedTag)">+</a>' +
                    '</li>' +
                  '</ul>' +
                '</div>' +
              '</div>';
          },
          link: function($scope) {
            if (!$scope.placeholder) {
              $scope.placeholder = '';
            }

            $scope.newTag     = '';
            $scope.invalidTag = false;

            /**
             * Change the current language.
             *
             * @param {Object} $event The language value.
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

            $scope.validateTag = function(e) {
              if (e.keyCode !== 13) {
                return null;
              }
              e.preventDefault();
              if ($scope.newTag === '') {
                $scope.invalidTag = false;
                return null;
              }

              var callback = function(response) {
                if (typeof response === 'object') {
                  $scope.invalidTag = true;
                } else {
                  $scope.invalidTag = !response;
                  if (response) {
                    $scope.ngModel.push({ name: $scope.newTag, language_id: $scope.locale });
                    $scope.newTag = '';
                  }
                }
              };

              this.checkNewTags(callback, $scope.newTag, $scope.locale);
              return null;
            };

            $scope.getLocalSuggestedTags = function(tagName) {
              var suggestedTags = $scope.getSuggestedTags($scope.locale, tagName, $scope.ngModel);

              return suggestedTags;
            };

            $scope.suggestedTagAccepted = function($item) {
              if (!($item.id in $scope.tagsList)) {
                $scope.tagsList[$item.id] = $item;
              }
              $scope.ngModel.push($item.id);
              $scope.newTag = '';
              return null;
            };

            /**
             * Change the current language.
             *
             * @param {Object} $event The language value.
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

            $scope.getTagIdsList = function() {
              return JSON.stringify($scope.ngModel);
            };
          },
        };
      }
    ]);
})();

