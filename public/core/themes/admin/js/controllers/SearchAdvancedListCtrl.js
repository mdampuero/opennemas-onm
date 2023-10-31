(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SearchAdvancedListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Controller for file list.
     */
    .controller('SearchAdvancedListCtrl', [
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
         * @memberOf SearchAdvancedListCtrl
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
         * @memberOf SearchAdvancedListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
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
         * @function isSelectable
         * @memberOf ListCtrl
         *
         * @description
         *   Checks if the item is selectable.
         *
         * @param {Object} item The item to check.
         *
         * @return {Boolean} True if the item is selectable. False otherwise.
         */
                $scope.isSelectable = function() {
                  return false;
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
