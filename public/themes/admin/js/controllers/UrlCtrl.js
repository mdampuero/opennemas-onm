(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  UserGroupCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $window
     * @requires cleaner
     * @requires http
     * @requires messenger
     * @requires routing
     *
     * @description
     *   Handles all actions in user groups listing.
     */
    .controller('UrlCtrl', [
      '$controller', '$scope',
      function($controller, $scope) {
        // Initialize the super class and extend it
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf UserGroupCtrl
         *
         * @description
         *   The user group object..
         *
         * @type {Object}
         */
        $scope.item = {
          redirect: 1,
          type: 0
        };

        /**
         * @memberOf SubscriptionCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          create:   'api_v1_backend_url_create',
          redirect: 'backend_url_show',
          save:     'api_v1_backend_url_save',
          show:     'api_v1_backend_url_show',
          update:   'api_v1_backend_url_update'
        };
      }
    ]);
})();
