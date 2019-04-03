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

        $scope.data = {items: []};

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

          $scope.setMode('grid');

          $scope.list();
        };

        /**
         * @function list
         * @memberOf RestListCtrl
         *
         * @description
         *   Reloads the list.
         */
        $scope.list = function() {
          $scope.flags.http.loading = 1;
          var append = $scope.mode === 'grid';

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: $scope.routes.list,
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            var data = response.data;

            if (!data.items && !append) {
              $scope.data.items = [];
            }

            if (append) {
              var oldItems = $scope.data.items;

              $scope.data = data;
              $scope.data.items = oldItems.concat(data.items);
            } else {
              $scope.data = data;
            }

            $scope.items = $scope.data.items;

            $scope.parseList(response.data);
            $scope.disableFlags('http');

            // Scroll top
            if (!append) {
              $('body').animate({ scrollTop: '0px' }, 1000);
            }
          }, function(response) {
            messenger.post(response.data);
            $scope.disableFlags('http');
            $scope.items = [];
          });
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
          var padding   = 40;

          if ($('.content-wrapper').length > 0) {
            maxWidth -= parseInt($('.content-wrapper').css('padding-right'));
          }

          var containerBaseSize = 150;
          var containerSize = $('.infinite-col').width();

          if (containerBaseSize > containerSize) {
            containerSize = containerBaseSize;
          }

          var height = containerSize + padding;
          var width = containerSize + padding;

          var rows = Math.ceil(maxHeight / height);
          var cols = Math.floor(maxWidth / width);

          if (rows === 0) {
            rows = 1;
          }

          if (cols === 0) {
            cols = 1;
          }

          if ($scope.criteria.epp !== rows * cols && $scope.data) {
            $scope.data.items = [];
          }

          $scope.criteria.epp = rows * cols;
        };

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
