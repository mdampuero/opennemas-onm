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

        $scope.data = { items: [] };

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          content_type_name: 'album',
          epp: 10,
          in_litter: 0,
          pk_fk_content_category: null,
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

          $scope.setMode('grid');
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
        }, function(nv) {
          if (nv !== 'grid') {
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
