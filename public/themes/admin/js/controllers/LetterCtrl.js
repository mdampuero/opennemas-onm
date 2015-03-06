/**
 * Handle actions for poll inner form.
 */
angular.module('BackendApp.controllers').controller('LetterCtarl', [
  '$controller', '$rootScope', '$scope',
  function($controller, $rootScope, $scope) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * Updates scope when photo1 changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('photo1', function(nv, ov) {
      $scope.img1        = null;

      if ($scope.photo1) {
        $scope.img1        = $scope.photo1.id;
      }
    }, true);
  }
]);
