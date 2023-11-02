(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  GlobalSearchListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Controller for file list.
     */
    .controller('GlobalSearchListCtrl', [
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
          content_type_name: 'article',
          category_id: null,
          epp: 10,
          in_litter: 0,
          orderBy: { created: 'desc' },
          page: 1
        };

        /**
         * @memberOf GlobalSearchListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          getList:    'api_v1_backend_article_get_list',
        };

        /**
         * @function init
         * @memberOf GlobalSearchListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function(contentTypes) {
          $scope.types              = contentTypes;
          $scope.backup.criteria    = $scope.criteria;
          $scope.app.columns.hidden = [ 'author', 'tags', 'content_views', 'starttime', 'endtime', 'category' ];

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

        $scope.getContentTypeName = function(contentTypeName) {
          var value = $scope.types.filter(function(element) {
            return element.value === contentTypeName;
          });

          return value[0].name;
        };
      }
    ]);
})();
