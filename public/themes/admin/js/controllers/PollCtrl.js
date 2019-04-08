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
    '$controller', '$rootScope', '$scope',
    function($controller, $rootScope, $scope) {
      'use strict';

      // Initialize the super class and extend it.
      $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

      /**
       * @function addAnswer
       * @memberOf PollCtrl
       *
       * @description
       *   Adds an empty answer to the answer list.
       */
      $scope.addAnswer = function() {
        $scope.answers.push({ pk_item: '', votes: 0, item: '' });
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
