angular.module('BackendApp.controllers')

  /**
   * @ngdoc controller
   * @name  PollCtrl
   *
   * @description
   *   Handles actions for poll inner
   *
   * @requires $controller
   * @requires $rootScope
   * @requires $scope
   */
  .controller('PollCtrl', [
    '$controller', '$rootScope', '$scope', '$timeout',
    function($controller, $rootScope, $scope, $timeout) {
      'use strict';

      // Initialize the super class and extend it.
      $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

      /**
       * @function init
       * @memberOf PollCtrl
       * Method to init the poll controller
       *
       * @param {object} poll   Poll to edit
       * @param {String} locale Locale for the poll
       * @param {Array}  tags   Array with all the tags needed for the poll
       */
      $scope.init = function(poll, locale, tags) {
        $scope.tag_ids = poll !== null ? poll.tag_ids : [];
        $scope.locale  = locale;
        $scope.tags    = tags;
      };

      /**
       * @function addAnswer
       * @memberOf PollCtrl
       *
       * @description
       *   Adds an empty answer to the answer list.
       */
      $scope.addAnswer = function() {
        $scope.answers.push({
          pk_item: '', votes: 0, item: ''
        });
      };

      /**
       * @function parseAnswers
       * @memberOf PollCtrl
       *
       * @description
       *   Parses the answers from the template and initializes the scope.
       *
       * @param {Object} name The poll answers.
       */
      $scope.parseAnswers = function(answers) {
        if (answers === null) {
          $scope.answers = [];
        } else {
          $scope.answers = answers;
        }
      };

      /**
       * @function removeAnswer
       * @memberOf PollCtrl
       *
       * @description
       *   Removes one answer from the answer list given its index.
       *
       * @param {Integer} index The index of the answer to remove.
       */
      $scope.removeAnswer = function(index) {
        $scope.answers.splice(index, 1);
      };

      /**
       * @function getTagsAutoSuggestedFields
       * @memberOf PollCtrl
       *
       * @description
       *  Method to method to retrieve th title for the autosuggested words
       */
      $scope.getTagsAutoSuggestedFields = function() {
        return $scope.title;
      };

      /**
       * @function loadAutoSuggestedTags
       * @memberOf PollCtrl
       *
       * @description
       *   Retrieve all auto suggested words for this poll
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

      // Updates internal parsedAnswers parameter when answers change.
      $scope.$watch('answers', function() {
        $scope.parsedAnswers = [];
        for (var i = $scope.answers.length - 1; i >= 0; i--) {
          $scope.parsedAnswers.push({
            pk_item: $scope.answers[i].pk_item,
            votes: $scope.answers[i].votes,
            item: $scope.answers[i].item
          });
        }
        $scope.parsedAnswers = JSON.stringify($scope.parsedAnswers.reverse());
      }, true);
    }
  ]);
