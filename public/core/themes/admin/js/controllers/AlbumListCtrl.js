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
      '$controller', '$scope', '$window', 'oqlEncoder', 'routing',
      function($controller, $scope, $window, oqlEncoder, routing) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestListCtrl', { $scope: $scope }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          content_type_name: 'album',
          category_id: null,
          epp: 10,
          in_litter: 0,
          orderBy: { created: 'desc' },
          page: 1,
          tag: null
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
          deleteItem: 'api_v1_backend_album_delete_item',
          deleteList: 'api_v1_backend_album_delete_list',
          getList:    'api_v1_backend_album_get_list',
          patchItem:  'api_v1_backend_album_patch_item',
          patchList:  'api_v1_backend_album_patch_list',
          public:     'frontend_album_show'
        };

        /**
         * @function getFrontendUrl
         * @memberOf AlbumListCtrl
         *
         * @description
         *   Generates the public URL basing on the item.
         *
         * @param {String} item  The item to generate route for.
         *
         * @return {String} The URL for the content.
         */
        $scope.getFrontendUrl = function(item) {
          if (!$scope.categories) {
            return '';
          }

          var categories = $scope.categories.filter(function(e) {
            return e.id === item.categories[0];
          });

          if (categories.length === 0) {
            return '';
          }

          return $scope.getSubdirectoryUrl(
            $scope.getL10nUrl(
              routing.generate($scope.routes.public, {
                id: item.pk_content,
                created: $window.moment(item.created).format('YYYYMMDDHHmmss'),
                slug: item.slug,
                category_slug: categories[0].name
              })
            )
          );
        };

        /**
         * @function init
         * @memberOf AlbumListCtrl
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
