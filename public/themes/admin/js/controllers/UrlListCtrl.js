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
          delete:         'api_v1_backend_url_delete',
          deleteSelected: 'api_v1_backend_urls_delete',
          list:           'api_v1_backend_urls_list',
          patch:          'api_v1_backend_url_patch',
          patchSelected:  'api_v1_backend_urls_patch'
        };

        /**
         * @function init
         * @memberOf UrlListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.columns.key      = 'url-columns';
          $scope.criteria.orderBy = { id: 'asc' };
          $scope.backup.criteria  = $scope.criteria;

          oqlEncoder.configure({ placeholder: {
            source: 'source ~ "[value]" or target ~ "[value]"'
          } });
          $scope.list();
        };
      }
    ]);
})();
