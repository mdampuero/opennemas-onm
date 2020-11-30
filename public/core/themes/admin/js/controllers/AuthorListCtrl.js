(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name AuthorListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Handles all actions in authors list.
     */
    .controller('AuthorListCtrl', [
      '$controller', '$scope', 'oqlEncoder',
      function($controller, $scope, oqlEncoder) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @memberOf AuthorListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_author_delete_item',
          deleteList: 'api_v1_backend_author_delete_list',
          getList:    'api_v1_backend_author_get_list',
          patchItem:  'api_v1_backend_author_patch_item',
          patchList:  'api_v1_backend_author_patch_list'
        };

        /**
         * @function init
         * @memberOf AuthorListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria = $scope.criteria;

          oqlEncoder.configure({
            placeholder: { name: 'name ~ "[value]" or username ~ "[value]"' }
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
