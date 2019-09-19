(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  StaticPageCtrl
     *
     * @description
     *   Handles actions for static page inner
     *
     * @requires $controller
     * @requires $rootScope
     * @requires $scope
     */
    .controller('StaticPageCtrl', [
      '$controller', '$scope', '$timeout',
      function($controller, $scope, $timeout) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf StaticPageCtrl
         *
         * @description
         *  The static page object.
         *
         * @type {Object}
         */
        $scope.item = {
          body: '',
          categories: [],
          content_status: 0,
          content_type_name: 'static_page',
          created: new Date(),
          description: '',
          endtime: null,
          favorite: 0,
          fk_content_type: 13,
          frontpage: 0,
          related_contents: [],
          starttime: null,
          tags: [],
          title: '',
          type: 0,
          with_comments: 0,
        };

        /**
         * @memberOf StaticPageCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          createItem: 'api_v1_backend_static_page_create_item',
          getItem:    'api_v1_backend_static_page_get_item',
          redirect:   'backend_static_page_show',
          saveItem:   'api_v1_backend_static_page_save_item',
          updateItem: 'api_v1_backend_static_page_update_item'
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.localize($scope.data.item, 'item', true);
        };
      }
    ]);
})();
