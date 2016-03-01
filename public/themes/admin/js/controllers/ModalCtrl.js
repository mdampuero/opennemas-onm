
angular.module('BackendApp.controllers').controller('modalCtrl', [
  '$modalInstance', '$scope', 'template', 'success',
  function ($modalInstance, $scope, template, success) {
    'use strict';

    $scope.template = template;

    $scope.Math = window.Math;

    /**
     * Closes the current modal
     */
    $scope.close = function(response) {
      $modalInstance.close(response);
    };

    /**
     * Confirms and executes the confirmed action.
     */
    $scope.confirm = function() {
        $scope.loading = 1;

        var getType = {};
        if (success && getType.toString.call(success) === '[object Function]') {
            success($modalInstance, $scope.template).then(function (response) {
                $modalInstance.close(response);
                $scope.loading = 0;
            });
        } else {
            $modalInstance.close(true);
        }
    };

    /**
     * Frees up memory before controller destroy event
     */
    $scope.$on('$destroy', function() {
        $scope.template = null;
    });
  }
]);
