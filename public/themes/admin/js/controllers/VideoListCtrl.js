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

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          content_type_name: 'video',
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

          $scope.list();
          $scope.setMode('grid');
        };

        /**
         * @inheritdoc
         */
        $scope.parseList = function(data) {
          $scope.configure(data.extra);
          $scope.localize($scope.data.items, 'items');
        };

        /**
         * @function select
         * @memberOf VideoListCtrl
         *
         * @description
         *   Adds and removes the item from the selected array.
         */
        $scope.select = function(item) {
          if ($scope.selected.items.indexOf(item.id) < 0) {
            $scope.selected.items.push(item.id);
          } else {
            $scope.selected.items = $scope.selected.items.filter(function(el) {
              return el != item.id;
            });
          }
        };

        /**
         * Changes the list mode.
         *
         * @param {String} mode The new list mode.
         */
        $scope.setMode = function(mode) {
          $scope.mode = mode;

          if (mode !== 'grid') {
            return;
          }

          var maxHeight = $(window).height() - $('.header').height() -
            $('.actions-navbar').height();
          var maxWidth  = $(window).width() - $('.sidebar').width();
          var padding   = 15;

          if ($('.content-wrapper').length > 0) {
            maxWidth -= parseInt($('.content-wrapper').css('padding-right'));
          }

          var height = $('.infinite-col').width() + padding;
          var width = $('.infinite-col').width() + padding;

          var rows = Math.ceil(maxHeight / height);
          var cols = Math.floor(maxWidth / width);

          if (rows === 0) {
            rows = 1;
          }

          if (cols === 0) {
            cols = 1;
          }

          if ($scope.criteria.epp !== rows * cols) {
            $scope.items = [];
          }

          $scope.criteria.epp = rows * cols;
        };
      }
    ]);
})();
