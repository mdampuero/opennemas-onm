(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SubscriberListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Handles all actions in subscribers list.
     */
    .controller('SubscriberListCtrl', [
      '$controller', '$scope', 'oqlEncoder',
      function($controller, $scope, oqlEncoder) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @memberOf SubscriberListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_subscriber_delete_item',
          deleteList: 'api_v1_backend_subscriber_delete_list',
          getList:    'api_v1_backend_subscriber_get_list',
          patchItem:  'api_v1_backend_subscriber_patch_item',
          patchList:  'api_v1_backend_subscriber_patch_list'
        };

        /**
         * @function confirm
         * @memberOf SubscriberListCtrl
         *
         * @description
         *   Confirm subscriber update.
         */
        $scope.confirm = function(property, value, item) {
          if (item) {
            $scope.patch(item, property, value);
            return;
          }
          $scope.patchSelected(property, value);
        };

        /**
         * @function init
         * @memberOf SubscriberListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria = $scope.criteria;

          oqlEncoder.configure({
            placeholder: {
              name: '(name ~ "[value]" or email ~ "[value]")',
              user_group_id: '([key] = "[value]" and status != 0)',
            }
          });

          $scope.list();
        };

        /**
         * @inheritdoc
         */
        $scope.isSelectable = function(item) {
          return $scope.backup.master ||
            $scope.getItemId(item) !== $scope.backup.id;
        };
      }
    ]);
})();
