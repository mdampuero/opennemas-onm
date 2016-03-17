
angular.module('BackendApp.controllers').controller('modalCtrl', [
  '$uibModalInstance', '$scope', 'template', 'success',
  function ($uibModalInstance, $scope, template, success) {
    'use strict';

    $scope.template = template;

    $scope.Math = window.Math;

    /**
     * Closes the current modal
     */
    $scope.close = function(response) {
      $uibModalInstance.close(response);
    };

    /**
     * Confirms and executes the confirmed action.
     */
    $scope.confirm = function() {
        $scope.loading = 1;

        var getType = {};
        if (success && getType.toString.call(success) === '[object Function]') {
            success($uibModalInstance, $scope.template).then(function (response) {
                $uibModalInstance.close(response);
                $scope.loading = 0;
            });
        } else {
            $uibModalInstance.close(true);
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
