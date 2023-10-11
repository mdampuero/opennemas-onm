(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  UserGroupListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     * @requires routing
     */
    .controller('NewsstandListCtrl', [
      '$controller', '$scope', '$window', 'oqlEncoder', 'routing',
      function($controller, $scope, $window, oqlEncoder, routing) {
        $.extend(this, $controller('ContentRestListCtrl', { $scope: $scope }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          id: null,
          content_type_name: 'kiosko',
          epp: 10,
          in_litter: 0,
          orderBy: { created:  'desc' },
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
          deleteItem: 'api_v1_backend_newsstand_delete_item',
          deleteList: 'api_v1_backend_newsstand_delete_list',
          getList:    'api_v1_backend_newsstand_get_list',
          patchItem:  'api_v1_backend_newsstand_patch_item',
          patchList:  'api_v1_backend_newsstand_patch_list',
          public:     'frontend_newsstand_show',
        };

        /**
         * @function getFrontendUrl
         * @memberOf NewsstandListCtrl
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
            return item.categories.indexOf(e.id) !== -1;
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
              category_slug: categoryName
            })
          );
        };

        /**
         * @function init
         * @memberOf NewsstandListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria    = $scope.criteria;
          $scope.app.columns.hidden = [ 'author', 'tags' ];

          oqlEncoder.configure({
            placeholder: {
              title: '[key] ~ "%[value]%"',
              created: '[key] ~ "%[value]%"'
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
          $scope.localize($scope.data.extra.categories, 'categories');
        };
      }
    ]);
})();
