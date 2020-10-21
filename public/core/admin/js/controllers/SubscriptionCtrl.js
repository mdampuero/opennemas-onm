(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SubscriptionCtrl
     *
     * @requires $controller
     * @requires $scope
     *
     * @description
     *   Provides actions to edit, save and update subscriptions.
     */
    .controller('SubscriptionCtrl', [
      '$controller', '$scope',
      function($controller, $scope) {
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
          createItem: 'api_v1_backend_subscription_create_item',
          getItem:    'api_v1_backend_subscription_get_item',
          list:       'backend_subscriptions_list',
          redirect:   'backend_subscription_show',
          saveItem:   'api_v1_backend_subscription_save_item',
          updateItem: 'api_v1_backend_subscription_update_item'
        };

        /**
         * @inheritdoc
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

          var privileges = $scope.data.extra.modules.FRONTEND
            .filter(function(e) {
              return e.name === name;
            });

          return privileges.length ?
            parseInt(privileges[0].id) : null;
        };
      }
    ]);
})();
