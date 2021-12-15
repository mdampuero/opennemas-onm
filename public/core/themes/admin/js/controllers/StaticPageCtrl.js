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
     * @requires $scope
     * @requires routing
     */
    .controller('StaticPageCtrl', [
      '$controller', '$scope', 'routing', 'translator',
      function($controller, $scope, routing, translator) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

        /**
         * @inheritdoc
         */
        $scope.draftEnabled = true;

        /**
         * @inheritdoc
         */
        $scope.draftKey = 'static-page-draft';

        /**
         * @inheritdoc
         */
        $scope.contentKey = 'staticpage';

        /**
         * @inheritdoc
         */
        $scope.dtm = null;

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
          created: null,
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
          with_comment: 0,
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
          list:       'backend_static_pages_list',
          public:     'frontend_static_page',
          redirect:   'backend_static_page_show',
          saveItem:   'api_v1_backend_static_page_save_item',
          updateItem: 'api_v1_backend_static_page_update_item'
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.checkFields();
          $scope.app.fields.staticpage.hidden =
            [
              'author', 'category', 'tags',
              'bodyLink', 'schedule', 'lists',
              'featuredFrontpage', 'featuredInner',
              'relatedFrontpage', 'relatedInner'
            ];

          $scope.localize($scope.data.item, 'item', true);

          if ($scope.draftKey !== null && $scope.data.item.pk_content) {
            $scope.draftKey = 'static-page-' + $scope.data.item.pk_content + '-draft';
          }

          $scope.checkDraft();
          translator.init($scope);
        };

        /**
         * @function getFrontendUrl
         * @memberOf StaticPageCtrl
         *
         * @description
         *   Generates the public URL basing on the item.
         *
         * @param {String} item  The item to generate route for.
         *
         * @return {String} The URL for the content.
         */
        $scope.getFrontendUrl = function(item) {
          return $scope.getL10nUrl(
            routing.generate($scope.routes.public, {
              slug: item.slug,
            })
          );
        };
      }
    ]);
})();
