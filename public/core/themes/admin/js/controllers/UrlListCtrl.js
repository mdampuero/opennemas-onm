(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  UrlListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Handles all actions in urls list.
     */
    .controller('UrlListCtrl', [
      '$controller', '$scope', 'oqlEncoder',
      function($controller, $scope, oqlEncoder) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @memberOf UrlListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_url_delete_item',
          deleteList: 'api_v1_backend_url_delete_list',
          getList:    'api_v1_backend_url_get_list',
          patchItem:  'api_v1_backend_url_patch_item',
          patchList:  'api_v1_backend_url_patch_list'
        };

        /**
         * @function init
         * @memberOf UrlListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.criteria.orderBy = { id: 'asc' };
          $scope.backup.criteria  = $scope.criteria;

          oqlEncoder.configure({ placeholder: {
            source: '(source ~ "[value]" or target ~ "[value]")'
          } });
          $scope.list();
        };
      }
    ]);
})();
