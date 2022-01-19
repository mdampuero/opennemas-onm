(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  FormSettingsModalCtrl
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
    .controller('FormSettingsModalCtrl', [
      '$uibModalInstance', '$scope', 'routing', 'success', 'template',
      function($uibModalInstance, $scope, routing, success, template) {
        $.extend(this, $controller('ModalCtrl', { $scope: $scope }));

        /**
         * @function confirm
         * @memberOf ModalCtrl
         *
         * @description
         *   Executes the success callback and closes the window returning the
         *   response from the callback.
         */
        $scope.confirm = function() {
          $scope.loading = 1;

          var getType = {};

          if (success && getType.toString.call(success) === '[object Function]') {
            success($uibModalInstance, $scope.template).then(function(response) {
              $scope.loading = 0;
              $uibModalInstance.close({
                data: response.data,
                headers: response.headers,
                status: response.status,
                success: true
              });
            }, function(response) {
              $scope.loading = 0;
              $uibModalInstance.close({
                data: response.data,
                headers: response.headers,
                status: response.status,
                success: false
              });
            });
          } else {
            $uibModalInstance.close(true);
          }
        };

        /**
         * @function dismiss
         * @memberOf ModalCtrl
         *
         * @description
         *   Closes the modal window without returning any response.
         */
        $scope.dismiss = function() {
          $uibModalInstance.dismiss();
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
})();
