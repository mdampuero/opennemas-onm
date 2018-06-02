/**
 * Handle actions for poll inner form.
 */
angular.module('BackendApp.controllers').controller('LetterCtrl', [
  '$controller', '$rootScope', '$scope', '$timeout',
  function($controller, $rootScope, $scope, $timeout) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * @function init
     * @memberOf LetterCtrl
     *
     * @description
     * Method to init the letter controller
     *
     * @param {object} letter   Letter to edit
     * @param {String} locale   Locale for the letter
     * @param {Array}  tags     Array with all the tags needed for the Letter
     */
    $scope.init = function(letter, locale, tags) {
      $scope.tag_ids = letter !== null ? letter.tag_ids : [];
      $scope.locale  = locale;
      $scope.tags    = tags;
    };

    /**
     * @function getTagsAutoSuggestedFields
     * @memberOf LetterCtrl
     *
     * @description
     *   Method to method to retrieve th title for the autosuggested words
     *
     */
    $scope.getTagsAutoSuggestedFields = function() {
      return $scope.title;
    };

    /**
     * @function loadAutoSuggestedTags
     * @memberOf LetterCtrl
     *
     * @description
     *   Retrieve all auto suggested words for this opinion
     *
     * @return {string} all words for the title
     */
    $scope.loadAutoSuggestedTags = function() {
      var data = $scope.getTagsAutoSuggestedFields();

      $scope.checkAutoSuggesterTags(
        function(items) {
          if (items !== null) {
            $scope.tag_ids = $scope.tag_ids.concat(items);
          }
        },
        data,
        $scope.tag_ids,
        $scope.locale
      );
    };

    /**
     * Updates scope when title changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('title', function(nv, ov) {
      if ($scope.tag_ids && $scope.tag_ids.length > 0 ||
          !nv || nv === ov) {
        return;
      }

      if ($scope.mtm) {
        $timeout.cancel($scope.mtm);
      }

      $scope.mtm = $timeout(function() {
        $scope.loadAutoSuggestedTags();
      }, 2500);
    });

    /**
     * Updates scope when photo1 changes.
     */
    $scope.$watch('photo1', function() {
      $scope.img1 = null;

      if ($scope.photo1) {
        $scope.img1 = $scope.photo1.id;
      }
    }, true);
  }
]);
