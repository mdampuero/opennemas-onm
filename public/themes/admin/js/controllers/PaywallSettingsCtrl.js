/**
 * Handle actions for cache config form.
 */
angular.module('BackendApp.controllers').controller('PaywallSettingsCtrl', [
  '$controller', '$rootScope', '$scope',
  function($controller, $rootScope, $scope) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));



    /**
     * Selects/unselects all instances.
     */
    $scope.selectAll = function() {
      if ($scope.selected.all) {
        $scope.selected.contents = Object.keys($scope.config);
      } else {
        $scope.selected.contents = [];
      }
    };
  }
]);
