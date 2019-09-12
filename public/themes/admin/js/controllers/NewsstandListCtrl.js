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
     */
    .controller('NewsstandListCtrl', [
      '$controller', '$scope', 'oqlEncoder',
      function($controller, $scope, oqlEncoder) {
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
          orderBy: { created:  'desc' },
          page: 1
        };

        /**
         * @inheritdoc
         */
        $scope.parseList = function(data) {
          $scope.configure(data.extra);
          $scope.localize($scope.data.items, 'items');
          $scope.localize($scope.data.extra.categories, 'categories');
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
          deleteItem: 'api_v1_backend_newsstand_delete_item',
          deleteList: 'api_v1_backend_newsstand_delete_list',
          getList:    'api_v1_backend_newsstand_get_list',
          patchItem:  'api_v1_backend_newsstand_patch_item',
          patchList:  'api_v1_backend_newsstand_patch_list'
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
