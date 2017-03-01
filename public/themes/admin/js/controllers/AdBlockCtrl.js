(function() {
  'use strict';

  /**
   * @ngdoc controller
   * @name  AdBlockCtrl
   *
   * @requires $uibModal
   * @requires $scope
   *
   * @description
   *   Detects ad blockers and show modal.
   */
  angular.module('BackendApp.controllers').controller('AdBlockCtrl', [
    '$uibModal', '$scope',
    function ($uibModal, $scope) {
      // Initialize add block
      var fuckAdBlock = new FuckAdBlock({
        debug: false,
        checkOnLoad: true,
        resetOnEnd: true
      });

      // Show modal when adblock detected
      fuckAdBlock.onDetected(function() {
        $uibModal.open({
          templateUrl: 'modal-adblock',
          backdrop: 'static',
          controller: 'modalCtrl',
          resolve: {
            template: function() { return null; },
            success: function() { return null; }
          }
        });
      });
    }
  ]);
})();
