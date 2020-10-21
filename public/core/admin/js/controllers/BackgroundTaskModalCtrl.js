(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  BackgroundTaskModalCtrl
     *
     * @requires $uibModalInstance
     * @requires $scope
     * @requires template
     * @requires success
     *
     * @description
     *   Handles actions for showing modal windows while running
     *   a callback in background.
     */
    .controller('BackgroundTaskModalCtrl', [
      '$uibModalInstance', '$scope', 'template', 'callback',
      function($uibModalInstance, $scope, template, callback) {
        /**
         * @memberOf BackgroundTaskModalCtrl
         *
         * @description
         *   The template parameters.
         *
         * @type {Object}
         */
        $scope.template = template;

        callback($uibModalInstance, template);

        /**
         * @function dismiss
         * @memberOf BackgroundTaskModalCtrl
         *
         * @description
         *   Close the modal without executing any action.
         */
        $scope.dismiss = function() {
          $uibModalInstance.dismiss();
        };

        // Frees up memory before controller destroy event
        $scope.$on('$destroy', function() {
          $scope.template = null;
        });
      }
    ]);
})();
