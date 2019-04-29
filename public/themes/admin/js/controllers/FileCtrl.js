/**
 * Handle actions for file inner.
 */
angular.module('BackendApp.controllers').controller('FileCtrl', [
  '$controller', '$rootScope', '$scope',
  function($controller, $rootScope, $scope) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));
  }
]);
