(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  UserGroupListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Handles all actions in user groups list.
     */
    .controller('UserGroupListCtrl', [
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
          delete:         'api_v1_backend_user_group_delete',
          deleteSelected: 'api_v1_backend_user_groups_delete',
          list:           'api_v1_backend_user_groups_list',
          patch:          'api_v1_backend_user_group_patch',
          patchSelected:  'api_v1_backend_user_groups_patch'
        };

        /**
         * @function getId
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *   Returns the item id.
         *
         * @param {Object} item The item.
         *
         * @return {Integer} The item id.
         */
        $scope.getId = function(item) {
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
          $scope.columns.key     = 'user-group-columns';
          $scope.backup.criteria = $scope.criteria;

          oqlEncoder.configure({ placeholder: { name: '[key] ~ "[value]"' } });
          $scope.list();
        };
      }
    ]);
})();
