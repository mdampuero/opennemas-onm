(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  ArticleCtrl
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
     *   Provides actions to edit, save and update articles.
     */
    .controller('ArticleCtrl', [
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
        $scope.draftKey = 'article-draft';

        /**
         * @inheritdoc
         */
        $scope.dtm = null;

        /**
         * @memberOf ArticleCtrl
         *
         * @description
         *  The article object.
         *
         * @type {Object}
         */
        $scope.item = {
          body: '',
          content_type_name: 'article',
          fk_content_type: 1,
          content_status: 0,
          description: '',
          frontpage: 0,
          created: null,
          starttime: null,
          endtime: null,
          title: '',
          title_int: '',
          type: 0,
          with_comment: 0,
          categories: [],
          related_contents: [],
          tags: [],
          external_link: '',
          agency: '',
        };

        /**
         * @memberOf ArticleCtrl
         *
         * @description
         *  The related service.
         *
         * @type {Object}
         */
        $scope.related = related;

        /**
         * @memberOf ArticleCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          createItem:  'api_v1_backend_article_create_item',
          getItem:     'api_v1_backend_article_get_item',
          getPreview:  'api_v1_backend_article_get_preview',
          list:        'backend_articles_list',
          public:      'frontend_article_show',
          redirect:    'backend_article_show',
          saveItem:    'api_v1_backend_article_save_item',
          savePreview: 'api_v1_backend_article_save_preview',
          updateItem:  'api_v1_backend_article_update_item'
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.localize($scope.data.item, 'item', true, [ 'related_contents' ]);
          $scope.expandFields();
          // Check if item is new (created) or existing for use default value or not
          if (!$scope.data.item.pk_content) {
            $scope.item.with_comment = $scope.data.extra.comments_enabled ? 1 : 0;
          }

          if ($scope.draftKey !== null && $scope.data.item.pk_content) {
            $scope.draftKey = 'article-' + $scope.data.item.pk_content + '-draft';
          }

          $scope.flags.block.title_int = $scope.item.title_int === $scope.item.title;

          $scope.checkDraft();
          related.init($scope);
          related.watch();
          translator.init($scope);
        };

        /**
         * @function getFrontendUrl
         * @memberOf ArticleCtrl
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
              slug: item.slug,
              category_slug: $scope.selectedCategory.name
            })
          );
        };

        /**
         * Opens a modal with the preview of the article.
         */
        $scope.preview = function() {
          $scope.flags.http.generating_preview = true;

          // Force ckeditor
          CKEDITOR.instances.body.updateElement();
          CKEDITOR.instances.description.updateElement();

          var status = { starttime: null, endtime: null, content_status: 1, with_comment: 0 };
          var item   = Object.assign({}, $scope.data.item, status);

          var data = {
            item: JSON.stringify(cleaner.clean(item)),
            locale: $scope.config.locale.selected
          };

          http.put($scope.routes.savePreview, data).then(function() {
            $uibModal.open({
              templateUrl: 'modal-preview',
              windowClass: 'modal-fullscreen',
              controller: 'ModalCtrl',
              resolve: {
                template: function() {
                  return {
                    src: routing.generate($scope.routes.getPreview)
                  };
                },
                success: function() {
                  return null;
                }
              }
            });

            $scope.flags.http.generating_preview = false;
          }, function() {
            $scope.flags.http.generating_preview = false;
          });
        };

        /**
         * @function undo
         * @memberOf ArticleCtrl
         *
         * @description
         *   Shows the change to be made on the input.
         */
        $scope.undo = function() {
          if ($scope.flags.block.title_int || $scope.previous) {
            return;
          }

          $scope.undoing        = true;
          $scope.previous       = $scope.item.title_int;
          $scope.item.title_int = $scope.item.title;
        };

        /**
         * @function redo
         * @memberOf ArticleCtrl
         *
         * @description
         *   Stops showing the change to be made to the input.
         */
        $scope.redo = function() {
          $scope.undoing = false;

          if ($scope.flags.block.title_int || !$scope.previous) {
            return;
          }

          $scope.item.title_int = $scope.previous;
          $scope.previous       = null;
        };

        // Update title int when block flag changes
        $scope.$watch('flags.block.title_int', function(nv) {
          $scope.previous = null;
          $scope.undoing  = false;

          if (!nv) {
            return;
          }

          $scope.item.title_int = $scope.item.title;
        });

        // Update title_int when title changes
        $scope.$watch('item.title', function(nv, ov) {
          // Mirror only when title_int locker is 'closed'
          if (!$scope.flags.block.title_int) {
            return;
          }

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

        $scope.$watch('config.locale.selected', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if ($scope.flags.block.title_int !== ($scope.item.title === $scope.item.title_int)) {
            $scope.flags.block.title_int = !$scope.flags.block.title_int;
          }
        });

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
