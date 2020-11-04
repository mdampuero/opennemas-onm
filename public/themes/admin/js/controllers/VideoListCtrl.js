(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  VideoListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     * @requires routing
     *
     * @description
     *   Controller for video list.
     */
    .controller('VideoListCtrl', [
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
          content_type_name: 'video',
          category_id: null,
          epp: 10,
          in_litter: 0,
          orderBy: { created: 'desc' },
          page: 1
        };

        /**
         * @memberOf VideoListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_video_delete_item',
          deleteList: 'api_v1_backend_video_delete_list',
          getList:    'api_v1_backend_video_get_list',
          patchItem:  'api_v1_backend_video_patch_item',
          patchList:  'api_v1_backend_video_patch_list',
          public:     'frontend_video_show'
        };

        /**
         * @function buildThumbnails
         * @memberOf VideoListCtrl
         *
         * @description
         *   Returns the thumbnail for a given content
         */
        $scope.buildThumbnails = function() {
          $scope.items.forEach(function(item) {
            if (item.related_contents.length > 0) {
              item.thumbnail = $scope.data.extra.related_contents[item.related_contents[0].target_id];
            }

            if (item.hasOwnProperty('information') && item.information.thumbnail) {
              item.thumbnail = item.information.thumbnail;
            }
          });
        };

        /**
         * @function getFrontendUrl
         * @memberOf VideoListCtrl
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

          return $scope.getL10nUrl(
            routing.generate($scope.routes.public, {
              id: item.pk_content,
              created: $window.moment(item.created).format('YYYYMMDDHHmmss'),
              slug: item.slug,
              category_slug: categories[0].name
            })
          );
        };

        /**
         * @function init
         * @memberOf VideoListCtrl
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
          $scope.buildThumbnails();
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
