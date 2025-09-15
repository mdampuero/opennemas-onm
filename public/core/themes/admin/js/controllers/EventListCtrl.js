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
      '$controller', '$scope', 'oqlEncoder', 'routing', '$window',
      function($controller, $scope, oqlEncoder, routing, $window) {
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
          saveItem:   'api_v1_backend_event_save_item',
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
            title: '[key] ~ "%[value]%"',
            starttime: '[key] > "[value]"',
            created: '[key] ~ "%[value]%"'
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
          if (!$scope.categories) {
            return '';
          }

          var categories = $scope.categories.filter(function(e) {
            return e.id === item.categories[0];
          });

          if (categories.length === 0) {
            return '';
          }

          var categoryName = categories[0].name;

          if ($scope.hasMultilanguage() && typeof categoryName === 'object') {
            categoryName = categoryName[$scope.config.locale.selected];
          }

          return $scope.data.extra.base_url + $scope.getL10nUrl(
            routing.generate($scope.routes.public, {
              id: item.pk_content.toString().padStart(6, '0'),
              created: item.urldatetime || $window.moment(item.created).format('YYYYMMDDHHmmss'),
              slug: item.slug,
              category_slug: categoryName
            })
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
