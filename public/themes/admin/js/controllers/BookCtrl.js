/**
 * Handle actions for poll inner form.
 */
angular.module('BackendApp.controllers').controller('BookCtrl', [
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
    $scope.$watch('book_cover', function() {
      $scope.book_cover_id = null;

      if ($scope.book_cover) {
        $scope.book_cover_id = $scope.book_cover.pk_content;
      }
    }, true);
  }
]);
