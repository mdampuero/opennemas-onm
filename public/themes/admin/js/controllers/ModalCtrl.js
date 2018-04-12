
angular.module('BackendApp.controllers').controller('modalCtrl', [
  '$uibModalInstance', '$scope', 'template', 'success',
  function($uibModalInstance, $scope, template, success) {
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
     * Closes the modal without returning response.
     */
    $scope.dismiss = function() {
      $uibModalInstance.dismiss();
    };

    /**
     * Confirms and executes the confirmed action.
     */
    $scope.confirm = function() {
      $scope.loading = 1;

      var getType = {};

      if (success && getType.toString.call(success) === '[object Function]') {
        success($uibModalInstance, $scope.template).then(function(response) {
          $scope.loading = 0;
          $uibModalInstance.close({ data: response.data, success: true });
        }, function(response) {
          $uibModalInstance.close({ data: response.data, success: false });
        });
      } else {
        $uibModalInstance.close(true);
      }
    };

    // Changes step on client saved
    $scope.$on('client-saved', function(event, args) {
      $scope.client = args;
      $scope.template.step = 2;
    });

    // Frees up memory before controller destroy event
    $scope.$on('$destroy', function() {
      $scope.template = null;
    });
  }
]);
