(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  VideoListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires messenger
     * @requires oqlEncoder
     * @requires queryManager
     *
     * @description
     *   Controller for video list.
     */
    .controller('VideoListCtrl', [
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
          content_type_name: 'video',
          pk_fk_content_category: null,
          epp: 10,
          in_litter: 0,
          orderBy: { created: 'desc' },
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
          delete:         'api_v1_backend_video_delete',
          deleteSelected: 'api_v1_backend_videos_delete',
          list:           'api_v1_backend_videos_list',
          patch:          'api_v1_backend_video_patch',
          patchSelected:  'api_v1_backend_videos_patch'
        };

        /**
         * @function init
         * @memberOf VideoListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.columns.key     = 'video-columns';
          $scope.backup.criteria = $scope.criteria;

          oqlEncoder.configure({ placeholder: {
            title: '[key] ~ "%[value]%"'
          } });

          if ($scope.config.mode === 'grid') {
            $scope.criteria.epp = $scope.getEppInGrid();
          }

          $scope.list();
        };

        /**
         * @inheritdoc
         */
        $scope.parseList = function(data) {
          $scope.configure(data.extra);
          $scope.localize($scope.data.items, 'items');
        };

        // Update epp when mode changes
        $scope.$watch(function() {
          return $scope.config.mode;
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

        // Change page when scrolling in grid mode
        $(window).scroll(function() {
          if (!$scope.mode || $scope.mode === 'list' ||
            $scope.items.length === $scope.data.total) {
            return;
          }

          if (!$scope.flags.http.loading && $(document).height() <
          $(window).height() + $(window).scrollTop()) {
            $scope.criteria.page++;
            $scope.$apply();
          }
        });
      }
    ]);
})();
