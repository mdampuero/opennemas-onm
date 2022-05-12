(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  CompanyListCtrl
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
     *   Provides actions to list companies.
     */
    .controller('CompanyListCtrl', [
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
          content_type_name: 'company',
          epp: 10,
          in_litter: 0,
          orderBy: { created:  'desc' },
          page: 1,
          tag: null
        };

        /**
         * @memberOf CompanyListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_company_delete_item',
          deleteList: 'api_v1_backend_company_delete_list',
          getList:    'api_v1_backend_company_get_list',
          patchItem:  'api_v1_backend_company_patch_item',
          patchList:  'api_v1_backend_company_patch_list',
          public:     'frontend_company_show'
        };

        /**
         * @function getFrontendUrl
         * @memberOf CompanyListCtrl
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
         * @memberOf CompanyListCtrl
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
          $scope.localize($scope.data.extra.categories, 'categories');
        };
      }
    ]);
})();
