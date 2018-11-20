/**
 * Handle actions for file inner.
 */
angular.module('BackendApp.controllers').controller('FileCtrl', [
  '$controller', '$rootScope', '$scope',
  function($controller, $rootScope, $scope) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * @function init
     * @memberOf FileCtrl
     *
     * @description
     * Method to init the file controller
     *
     * @param {object} file     File to edit
     * @param {String} locale   Locale for the file
     * @param {Array}  tags     Array with all the tags needed for the file
     */
    $scope.init = function(file, locale, tags) {
      $scope.locale = locale;
      $scope.tags   = tags;
    };
  }
]);
