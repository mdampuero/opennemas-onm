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
        $.extend(this, $controller('RestListCtrl', { $scope: $scope, }));

        /**
         * @memberOf SubscriberListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          delete:         'api_v1_backend_subscriber_delete',
          deleteSelected: 'api_v1_backend_subscribers_delete',
          list:           'api_v1_backend_subscribers_list',
          patch:          'api_v1_backend_subscriber_patch',
          patchSelected:  'api_v1_backend_subscribers_patch'
        };

        /**
         * @function init
         * @memberOf SubscriberListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.columns.key     = 'subscriber-columns';
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
         * @function isSelectable
         * @memberOf SubscriberListCtrl
         *
         * @description
         *   Checks if the item is selectable.
         *
         * @param {Object} item The item to check.
         *
         * @return {Boolean} True if the item is selectable. False otherwise.
         */
        $scope.isSelectable = function(item) {
          return $scope.backup.master ||
            $scope.getId(item) !== $scope.backup.id;
        };
      }
    ]);
})();
