(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  PressClippingCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     * @requires messenger
     *
     * @description
     *   Provides actions to list notifications.
     */
    .controller('PressClippingCtrl', [
      '$controller', '$scope', 'http', 'messenger', 'routing',
      function($controller, $scope, http, messenger, routing) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @memberof PressClippingCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          // TODO: Falta definir
        };

        // Initialize settings with pressclipping_service
        $scope.settings = {
          // TODO: Falta definir
        };

        /**
         * @function init
         * @memberof PressClippingCtrl
         *
         * @description
         *   Initializes the form.
         */
        $scope.init = function() {
          // TODO : Falta inicio
        };
      }
    ]);
})();
