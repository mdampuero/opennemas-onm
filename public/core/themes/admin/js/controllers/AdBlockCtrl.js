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
    '$controller', '$uibModal', '$scope',
    function($controller, $uibModal, $scope) {
      // Initialize the super class and extend it.
      $.extend(this, $controller('ContentListCtrl', { $scope: $scope }));

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
          controller: 'ModalCtrl',
          resolve: {
            template: function() {
              return null;
            },
            success: function() {
              return null;
            }
          }
        });
      });
    }
  ]);
})();
