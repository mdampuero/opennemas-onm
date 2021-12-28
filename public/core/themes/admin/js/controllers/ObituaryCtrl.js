(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ObituaryCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $timeout
     * @requires $uibModal
     * @requires $window
     * @requires cleaner
     * @requires http
     * @requires linker
     * @requires localizer
     * @requires messenger
     * @requires webStorage
     *
     * @description
     *   Provides actions to edit, save and update obituaries.
     */
    .controller('ObituaryCtrl', [
      '$controller', '$scope', '$timeout', '$uibModal', '$window', 'cleaner',
      'http', 'related', 'routing', 'translator',
      function($controller, $scope, $timeout, $uibModal, $window, cleaner,
          http, related, routing, translator) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

        /**
         * @inheritdoc
         */
        $scope.draftEnabled = true;

        /**
         * @inheritdoc
         */
        $scope.draftKey = 'obituary-draft';

        /**
         * @inheritdoc
         */
        $scope.dtm = null;

        /**
         * @inheritdoc
         */
        $scope.incomplete = true;

        /**
         * @memberOf ObituaryCtrl
         *
         * @description
         *  The article object.
         *
         * @type {Object}
         */
        $scope.item = {
          body: '',
          content_type_name: 'obituary',
          fk_content_type: 18,
          content_status: 0,
          description: '',
          frontpage: 0,
          created: null,
          starttime: null,
          endtime: null,
          title: '',
          type: 0,
          with_comment: 0,
          related_contents: [],
          tags: [],
          agency: '',
        };

        /**
         * @memberOf ObituaryCtrl
         *
         * @description
         *  The related service.
         *
         * @type {Object}
         */
        $scope.related = related;

        /**
         * @memberOf ObituaryCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          createItem:  'api_v1_backend_obituary_create_item',
          getItem:     'api_v1_backend_obituary_get_item',
          getPreview:  'api_v1_backend_obituary_get_preview',
          list:        'backend_obituaries_list',
          public:      'frontend_obituaries_show',
          redirect:    'backend_obituaries_show',
          saveItem:    'api_v1_backend_obituary_save_item',
          savePreview: 'api_v1_backend_obituary_save_preview',
          updateItem:  'api_v1_backend_obituary_update_item'
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.localize($scope.data.item, 'item', true, [ 'related_contents' ]);

          // Check if item is new (created) or existing for use default value or not
          if (!$scope.data.item.pk_content) {
            $scope.item.with_comment = $scope.data.extra.comments_enabled ? 1 : 0;
          }

          if ($scope.draftKey !== null && $scope.data.item.pk_content) {
            $scope.draftKey = 'article-' + $scope.data.item.pk_content + '-draft';
          }

          $scope.checkDraft();
          related.init($scope);
          related.watch();
          translator.init($scope);
        };

        /**
         * @function getFrontendUrl
         * @memberOf ObituaryCtrl
         *
         * @description
         *   Generates the public URL basing on the item.
         *
         * @param {String} item  The item to generate route for.
         *
         * @return {String} The URL for the content.
         */
        $scope.getFrontendUrl = function(item) {
          if (!$scope.selectedCategory) {
            return '';
          }

          return $scope.getL10nUrl(
            routing.generate($scope.routes.public, {
              id: item.pk_content,
              created: $window.moment(item.created).format('YYYYMMDDHHmmss'),
              slug: item.slug
            })
          );
        };

        // Update title_int when title changes
        $scope.$watch('item.title', function(nv, ov) {
          if (!nv && !ov) {
            return;
          }

          if (!$scope.item.title_int || ov === $scope.item.title_int) {
            $scope.item.title_int = nv;
          }

          if (!$scope.item.pk_content) {
            if ($scope.tm) {
              $timeout.cancel($scope.tm);
            }

            if (!nv) {
              $scope.item.slug = '';
            }
          }
        }, true);

        /**
         * @inheritdoc
         */
        $scope.validate = function() {
          if ($scope.form && $scope.form.$invalid) {
            $('[name=form]')[0].reportValidity();
            return false;
          }

          if (!$('[name=form]')[0].checkValidity()) {
            $('[name=form]')[0].reportValidity();
            return false;
          }

          return true;
        };
      }
    ]);
})();
