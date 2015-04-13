/**
 * Handle actions for poll inner form.
 */
angular.module('BackendApp.controllers').controller('PollCtrl', [
  '$controller', '$rootScope', '$scope',
  function($controller, $rootScope, $scope) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * Adds an empty answer to the answers list
     *
     * @param Object answers The poll answers.
     */
    $scope.addAnswer = function () {
      $scope.answers.push({pk_item: '', votes: 0, item: ''});
    };

    /**
     * Removes one answer fron the list given its index
     *
     * @param int the answer index in the list
     */
    $scope.removeAnswer = function (index) {
      $scope.answers.splice(index, 1);
    };

    /**
     * Parse the answers from template and initialize the scope properly
     *
     * @param Object answers The poll answers.
     */
    $scope.parseAnswers = function(answers) {
      if (answers === null) {
        $scope.answers = [];
      } else {
        $scope.answers = answers;
      }
    };

    /**
     * Updates internal parsedAnswers parameter when answers change.
     *
     * @param Object nv The new values.
     * @param Object ov The old values.
     */
    $scope.$watch('answers', function(nv, ov) {
      if (nv === ov) {
        return false;
      }

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
