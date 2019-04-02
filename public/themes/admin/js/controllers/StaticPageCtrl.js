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
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          create:   'api_v1_backend_static_page_create',
          redirect: 'backend_static_page_show',
          save:     'api_v1_backend_static_page_save',
          show:     'api_v1_backend_static_page_show',
          update:   'api_v1_backend_static_page_update'
        };

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
          content_type_name: 'static_page',
          fk_content_type: 13,
          content_status: 0,
          description: '',
          favorite: 0,
          frontpage: 0,
          created: new Date(),
          starttime: null,
          endtime: null,
          thumbnail: null,
          title: '',
          type: 0,
          with_comments: 0,
          categories: [],
          related_contents: [],
          tags: [],
        };

        /**
         * @function parseItem
         * @memberOf StaticPageCtrl
         *
         * @description
         *   Parses the response and adds information to the scope.
         *
         * @param {Object} data The data in the response.
         */
        $scope.parseItem = function(data) {
          if (data.item) {
            $scope.data.item = angular.extend($scope.item, data.item);
          }

          $scope.configure(data.extra);
          $scope.localize($scope.data.item, 'item', true);
        };
      }
    ]);
})();
