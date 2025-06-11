(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SubscriberModalCtrl
     *
     * @requires $uibModalInstance
     * @requires $scope
     * @requires routing
     * @requires success
     * @requires template
     *
     * @description
     *   Controller for News Agency listing.
     */
    .controller('SubscriberModalCtrl', [
      '$uibModalInstance', '$scope', 'template', 'success',
      function($uibModalInstance, $scope, template, success) {
        /**
         * MemberOf modalCtrl
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

        $scope.close = function(response) {
          $uibModalInstance.close(response);
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
