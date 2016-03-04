(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  modalCtrl
     *
     * @requires $uibModalInstance
     * @requires $scope
     * @requires template
     * @requires success
     *
     * @description
     *   Handles actions for confirmation modal windows.
     */
    .controller('modalCtrl', [
      '$uibModalInstance', '$scope', 'template', 'success',
      function ($uibModalInstance, $scope, template, success) {
        /**
         * memberOf modalCtrl
         *
         * @description
         *   The template parameters.
         *
         * @type {Object}
         */
        $scope.template = template;

        /**
         * @function dismiss
         * @memberOf modalCtrl
         *
         * @description
         *   Close the modal without returning response.
         */
        $scope.dismiss = function() {
          $uibModalInstance.dismiss();
        };

        /**
         * @function confirm
         * @memberOf modalCtrl
         *
         * @description
         *   Confirms and executes the confirmed action.
         */
        $scope.confirm = function() {
          $scope.loading = 1;

          var getType = {};
          if (success && getType.toString.call(success) === '[object Function]') {
            success($uibModalInstance, $scope.template);
          } else {
            $uibModalInstance.close(true);
          }
        };

        // Frees up memory before controller destroy event
        $scope.$on('$destroy', function() {
          $scope.template = null;
        });
      }
    ]);
})();

