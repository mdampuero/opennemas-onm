(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ImageListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Controller for album list.
     */
    .controller('PhotoListCtrl', [
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
          content_type_name: 'photo',
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
          deleteItem: 'api_v1_backend_photo_delete_item',
          deleteList: 'api_v1_backend_photo_delete_list',
          getList:    'api_v1_backend_photo_get_list',
          patchItem:  'api_v1_backend_photo_patch_item',
          patchList:  'api_v1_backend_photo_patch_list'
        };

        /**
         * @function init
         * @memberOf ImageListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria    = $scope.criteria;
          $scope.app.columns.hidden = [];

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
