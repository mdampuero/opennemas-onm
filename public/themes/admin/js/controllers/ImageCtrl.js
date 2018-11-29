/**
 * Handle actions for image inner.
 */
angular.module('BackendApp.controllers').controller('ImageCtrl', [
  '$controller', '$rootScope', '$scope',
  function($controller, $rootScope, $scope) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * @function init
     * @memberOf ImageCtrl
     *
     * @description
     * Method to init the image controller
     *
     * @param {object} image    image to edit
     * @param {String} locale   Locale for the image
     * @param {Array}  tags     Array with all the tags needed for the image
     */
    $scope.init = function(images, locale, tags) {
      $scope.locale = locale;
      $scope.tags   = tags;
    };
  }
]);
