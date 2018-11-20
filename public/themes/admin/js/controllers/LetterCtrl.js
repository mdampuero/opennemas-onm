/**
 * Handle actions for poll inner form.
 */
angular.module('BackendApp.controllers').controller('LetterCtrl', [
  '$controller', '$rootScope', '$scope',
  function($controller, $rootScope, $scope) {
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
      $scope.locale = locale;
      $scope.tags   = tags;
    };

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
