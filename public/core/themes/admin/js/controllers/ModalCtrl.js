(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ModalCtrl
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
    .controller('ModalCtrl', [
      '$uibModalInstance', '$scope', '$q', 'routing', 'success', 'template',
      function($uibModalInstance, $scope, $q, routing, success, template) {
        /**
         * @memberOf ModalCtrl
         *
         * @description
         *   The routing service.
         *
         * @type {Object}
         */
        $scope.routing = routing;

        /**
         * @memberOf ModalCtrl
         *
         * @description
         *  The information provided by the controller which open the modal
         *  window.
         *
         * @type {Object}
         */
        $scope.template = template;

        /**
         * @function close
         * @memberOf ModalCtrl
         *
         * @description
         *   Closes the modal window returning the provided response to the
         *   controller which opened the modal window.
         *
         * @param {Object} response The response to return to the main
         *                          controller.
         */
        $scope.close = function(response) {
          $uibModalInstance.close(response);
        };

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

          if (!success || !getType.toString.call(success) === '[object Function]') {
            $uibModalInstance.close(true);
            return;
          }

          $q.when(success($uibModalInstance, $scope.template))
            .then(function(response) {
              $scope.resolve(response, true);
            }, function(response) {
              $scope.resolve(response, false);
            });
        };

        $scope.resolve = function(response, success) {
          $scope.loading = 0;

          if (!response || Object.keys(response) > 0) {
            $uibModalInstance.close(success);
            return;
          }

          $uibModalInstance.close({
            data: response.data,
            headers: response.headers,
            status: response.status,
            success: success
          });
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
