(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SubscriptionListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Handles all actions in subscriptions list.
     */
    .controller('SubscriptionListCtrl', [
      '$controller', '$scope', 'oqlEncoder',
      function($controller, $scope, oqlEncoder) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_subscription_delete_item',
          deleteList: 'api_v1_backend_subscription_delete_list',
          getList:    'api_v1_backend_subscription_get_list',
          patchItem:  'api_v1_backend_subscription_patch_item',
          patchList:  'api_v1_backend_subscription_patch_list'
        };

        /**
         * @inheritdoc
         */
        $scope.getItemId = function(item) {
          return item.pk_user_group;
        };

        /**
         * @function init
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria = $scope.criteria;

          oqlEncoder.configure({ placeholder: { name: '[key] ~ "[value]"' } });
          $scope.list();
        };
      }
    ]);
})();
