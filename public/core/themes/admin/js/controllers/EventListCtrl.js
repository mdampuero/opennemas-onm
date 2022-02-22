(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  EventListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Handles all actions in user groups list.
     */
    .controller('EventListCtrl', [
      '$controller', '$scope', 'oqlEncoder', 'routing',
      function($controller, $scope, oqlEncoder, routing) {
        $.extend(this, $controller('ContentRestListCtrl', { $scope: $scope }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          content_type_name: 'event',
          epp: 10,
          in_litter: 0,
          orderBy: { created: 'desc' },
          page: 1,
          tag: null
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
          deleteItem: 'api_v1_backend_event_delete_item',
          deleteList: 'api_v1_backend_event_delete_list',
          getList:    'api_v1_backend_event_get_list',
          patchItem:  'api_v1_backend_event_patch_item',
          patchList:  'api_v1_backend_event_patch_list',
          public:     'frontend_event_show'
        };

        /**
         * @function init
         * @memberOf EventListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria    = $scope.criteria;
          $scope.app.columns.hidden = [];

          oqlEncoder.configure({ placeholder: {
            title: '[key] ~ "%[value]%"'
          } });

          $scope.list();
        };

        /**
         * @function getFrontendUrl
         * @memberOf EventCtrl
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
                slug: item.slug,
              })
            )
          );
        };

        /**
         * @inheritdoc
         */
        $scope.parseList = function(data) {
          $scope.configure(data.extra);
          $scope.localize($scope.data.items, 'items');
          $scope.localize($scope.data.extra.categories, 'categories');
        };
      }
    ]);
})();
