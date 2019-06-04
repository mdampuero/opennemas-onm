(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  AlbumListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Controller for album list.
     */
    .controller('AlbumListCtrl', [
      '$controller', '$scope', 'oqlEncoder',
      function($controller, $scope, oqlEncoder) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestListCtrl', { $scope: $scope }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          content_type_name: 'album',
          pk_fk_content_category: null,
          epp: 10,
          in_litter: 0,
          orderBy: { created: 'desc' },
          page: 1
        };

        /**
         * @memberOf AlbumListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          delete:         'api_v1_backend_album_delete',
          deleteSelected: 'api_v1_backend_albums_delete',
          list:           'api_v1_backend_albums_list',
          patch:          'api_v1_backend_album_patch',
          patchSelected:  'api_v1_backend_albums_patch'
        };

        /**
         * @function init
         * @memberOf AlbumListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.columns.key     = 'album-columns';
          $scope.backup.criteria = $scope.criteria;

          oqlEncoder.configure({
            placeholder: {
              title: '[key] ~ "%[value]%"'
            }
          });

          if ($scope.app.mode === 'grid') {
            $scope.criteria.epp = $scope.getEppInGrid();
          }

          $scope.list();
        };

        /**
         * @inheritdoc
         */
        $scope.isModeSupported = function() {
          return true;
        };

        /**
         * @inheritdoc
         */
        $scope.parseList = function(data) {
          $scope.configure(data.extra);
          $scope.localize($scope.data.items, 'items');
          $scope.localize($scope.data.extra.categories, 'categories');
        };

        // Update epp when mode changes
        $scope.$watch(function() {
          return $scope.app.mode;
        }, function(nv, ov) {
          if (nv === ov) {
            return;
          }

          var epp = $scope.getEppInGrid();

          if (epp !== $scope.criteria.epp) {
            $scope.flags.http.loading = true;
            $scope.criteria.epp = nv === 'grid' ? epp : 10;
          }
        }, true);
      }
    ]);
})();
