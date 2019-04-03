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
     *    Method to init the image controller
     *
     * @param {object} photo The photo to edit.
     */
    $scope.init = function(photo) {
      $scope.photo = photo;
    };
  }
]);
