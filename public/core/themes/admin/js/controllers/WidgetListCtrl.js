(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  WidgetListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Controller for file list.
     */
    .controller('WidgetListCtrl', [
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
          content_type_name: 'widget',
          category_id: null,
          renderlet: null,
          epp: 10,
          in_litter: 0,
          orderBy: { created: 'desc' },
          page: 1
        };

        /**
         * @memberOf WidgetListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_widget_delete_item',
          deleteList: 'api_v1_backend_widget_delete_list',
          getList:    'api_v1_backend_widget_get_list',
          patchItem:  'api_v1_backend_widget_patch_item',
          patchList:  'api_v1_backend_widget_patch_list'
        };

        /**
         * @inheritdoc
         */
        $scope.hasMultilanguage = function() {
          return false;
        };

        /**
         * @function init
         * @memberOf WidgetListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria    = $scope.criteria;
          $scope.app.columns.hidden = [ 'author', 'category', 'tags' ];

          oqlEncoder.configure({
            placeholder: {
              title: '[key] ~ "%[value]%"'
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
