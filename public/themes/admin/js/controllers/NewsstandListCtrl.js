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
    .controller('NewsstandListCtrl', [
      '$controller', '$scope', 'oqlEncoder', 'linker', 'localizer',
      function($controller, $scope, oqlEncoder, linker, localizer) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          pk_content_category: null,
          content_type_name: 'kiosko',
          epp: 10,
          in_litter: 0,
          orderBy: { starttime:  'desc' },
          page: 1
        };

        /**
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_newsstand_delete',
          deleteList: 'api_v1_backend_newsstands_delete',
          getList:    'api_v1_backend_newsstands_list',
          patchItem:  'api_v1_backend_newsstand_patch',
          patchList:  'api_v1_backend_newsstands_patch'
        };

        /**
         * @function init
         * @memberOf NewsstandListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria = $scope.criteria;

          oqlEncoder.configure({ placeholder: { title: '[key] ~ "%[value]%"' } });
          $scope.list();
        };
      }
    ]);
})();
