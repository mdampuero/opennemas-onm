(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ModalWidgetEditCtrl
     *
     * @requires $uibModalInstance
     * @requires $scope
     * @requires fullUrl
     *
     * @description
     *   Controller for the widget edit modal.
     */
    .controller('ModalWidgetEditCtrl', [
      '$uibModalInstance', '$scope', '$timeout', 'fullUrl',
      function($uibModalInstance, $scope, $timeout, fullUrl) {
        $scope.close = function() {
          location.reload();
          $uibModalInstance.dismiss('cancel');
        };

        $timeout(function() {
          document.getElementById('modal-iframe').src = fullUrl;
        }, 0);
      }
    ]);
})();
