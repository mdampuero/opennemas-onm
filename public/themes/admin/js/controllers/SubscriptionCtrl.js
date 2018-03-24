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
         * @memberOf RestInnerCtrl
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
         * @function itemHasId
         * @memberOf RestInnerCtrl
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

        /**
         * @function getItem
         * @memberOf SubscriptionCtrl
         *
         * @description
         *   Gets the subscription to show.
         *
         * @param {Integer} id The subscription id.
         */
        $scope.parseItem = function(data) {
          if (data.subscription) {
            $scope.item = data.subscription;
          }
        };
      }
    ]);
})();
