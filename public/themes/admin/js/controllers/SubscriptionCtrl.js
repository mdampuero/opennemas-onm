(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SubscriptionCtrl
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
     *   Provides actions to edit, save and update subscriptions.
     */
    .controller('SubscriptionCtrl', [
      '$controller', '$scope', '$window', 'cleaner', 'http', 'messenger', 'routing',
      function($controller, $scope, $window, cleaner, http, messenger, routing) {
        // Initialize the super class and extend it
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf SubscriptionCtrl
         *
         * @description
         *  The subscription object.
         *
         * @type {Object}
         */
        $scope.item = {
          privileges: [],
          type: 1
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
          create:   'api_v1_backend_subscription_create',
          redirect: 'backend_subscription_show',
          save:     'api_v1_backend_subscription_save',
          show:     'api_v1_backend_subscription_show',
          update:   'api_v1_backend_subscription_update'
        };

        /**
         * @function getItemId
         * @memberOf SubscriptionCtrl
         *
         * @description
         *   Returns the item id.
         *
         * @return {Integer} The item id.
         */
        $scope.getItemId = function() {
          return $scope.item.pk_user_group;
        };

        /**
         * @function getPermissionId
         * @memberOf SubscriptionCtrl
         *
         * @description
         *   Returns the permission id basing on the permission name.
         *
         * @param {String} name The permission name.
         *
         * @return {Integer} The permission id.
         */
        $scope.getPermissionId = function(name) {
          if (!$scope.data || !$scope.data.extra) {
            return null;
          }

          return parseInt($scope.data.extra.modules.FRONTEND
            .filter(function(e) {
              return e.name === name;
            })[0].pk_privilege);
        };

        /**
         * @function itemHasId
         * @memberOf SubscriptionCtrl
         *
         * @description
         *   Checks if the current item has an id.
         *
         * @return {Boolean} description
         */
        $scope.itemHasId = function() {
          return $scope.item.pk_user_group &&
            $scope.item.pk_user_group !== null;
        };
      }
    ]);
})();
