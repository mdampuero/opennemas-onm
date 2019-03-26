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
      '$controller', '$location', '$scope', 'http', 'messenger', 'oqlEncoder',
      function($controller, $location, $scope, http, messenger, oqlEncoder) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestListCtrl', { $scope: $scope }));

        $scope.mode = 'list';

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          content_type_name: 'video',
          epp: 10,
          in_litter: 0,
          orderBy: { starttime:  'desc' },
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

          $scope.criteria.orderBy = { created: 'asc' };

          oqlEncoder.configure({ placeholder: {
            title: '[key] ~ "%[value]%"'
          } });

          $scope.list();
        };

        /**
         * @inheritdoc
         */
        $scope.parseList = function(data) {
          $scope.configure(data.extra);
          $scope.localize($scope.data.items, 'items');
        };
      }
    ]);
})();
