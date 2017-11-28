(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  YesNoModalCtrl
     *
     * @requires $uibModalInstance
     * @requires $scope
     * @requires template
     * @requires success
     *
     * @description
     *   Handles actions for confirmation modal windows.
     */
    .controller('YesNoModalCtrl', [
      '$uibModalInstance', '$scope', 'template', 'yes', 'no',
      function($uibModalInstance, $scope, template, yes, no) {
        /**
         * @memberOf YesNoModalCtrl
         *
         * @description
         *   The template parameters.
         *
         * @type {Object}
         */
        $scope.template = template;

        /**
         * @function dismiss
         * @memberOf YesNoModalCtrl
         *
         * @description
         *   Close the modal without executing any action.
         */
        $scope.dismiss = function() {
          $uibModalInstance.dismiss();
        };

        /**
         * @function no
         * @memberOf YesNoModalCtrl
         *
         * @description
         *   Confirms and executes the confirmed action.
         */
        $scope.no = function() {
          $scope.noLoading = 1;

          var getType = {};

          if (no && getType.toString.call(no) === '[object Function]') {
            no($uibModalInstance, $scope.template, $scope.yesLoading);
          } else {
            $uibModalInstance.close(true);
          }
        };

        /**
         * @function no
         * @memberOf YesNoModalCtrl
         *
         * @description
         *   Confirms and executes the confirmed action.
         */
        $scope.yes = function() {
          $scope.yesLoading = 1;

          var getType = {};

          if (yes && getType.toString.call(yes) === '[object Function]') {
            yes($uibModalInstance, $scope.template, $scope.yesLoading);
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
