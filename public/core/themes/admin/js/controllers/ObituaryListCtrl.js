(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ArticleListCtrl
     *
     * @requires $controller
     * @requires $location
     * @requires $scope
     * @requires $timeout
     * @requires http
     * @requires messenger
     * @requires linker
     * @requires localizer
     * @requires oqlEncoder
     *
     * @description
     *   Provides actions to list articles.
     */
    .controller('ObituaryListCtrl', [
      '$controller', '$scope', 'oqlEncoder', 'routing', '$window',
      function($controller, $scope, oqlEncoder, routing, $window) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestListCtrl', { $scope: $scope }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          content_type_name: 'obituary',
          epp: 10,
          in_litter: 0,
          orderBy: { created:  'desc' },
          page: 1,
          tag: null
        };

        /**
         * @memberOf ObituaryListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_obituary_delete_item',
          deleteList: 'api_v1_backend_obituary_delete_list',
          getList:    'api_v1_backend_obituary_get_list',
          patchItem:  'api_v1_backend_obituary_patch_item',
          patchList:  'api_v1_backend_obituary_patch_list',
          public:     'frontend_obituary_show'
        };

        /**
         * @function getFrontendUrl
         * @memberOf ObituaryListCtrl
         *
         * @description
         *   Generates the public URL basing on the item.
         *
         * @param {String} item  The item to generate route for.
         *
         * @return {String} The URL for the content.
         */
        $scope.getFrontendUrl = function(item) {
          return $scope.getSubdirectoryUrl(
            $scope.getL10nUrl(
              routing.generate($scope.routes.public, {
                id: item.pk_content,
                created: $window.moment(item.created).format('YYYYMMDDHHmmss'),
                slug: item.slug,
              })
            )
          );
        };

        /**
         * @function init
         * @memberOf ObituaryListCtrl
         *
         * @description
         *   Configures and initializes the list.
         */
        $scope.init = function() {
          $scope.backup.criteria    = $scope.criteria;
          $scope.app.columns.hidden = [ 'category' ];

          oqlEncoder.configure({
            placeholder: {
              title: 'title ~ "%[value]%"'
            }
          });

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
