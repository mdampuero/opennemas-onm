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
          delete:         'api_v1_backend_author_delete',
          deleteSelected: 'api_v1_backend_authors_delete',
          list:           'api_v1_backend_authors_list',
          patch:          'api_v1_backend_author_patch',
          patchSelected:  'api_v1_backend_authors_patch'
        };

        /**
         * @function init
         * @memberOf AuthorListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.columns.key     = 'author-columns';
          $scope.backup.criteria = $scope.criteria;

          oqlEncoder.configure({
            placeholder: { name: 'name ~ "[value]" or username ~ "[value]"' }
          });

          $scope.list();
        };

        /**
         * @function isSelectable
         * @memberOf AuthorListCtrl
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
