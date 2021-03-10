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
      'http', 'related', 'routing', 'webStorage',
      function($controller, $scope, $timeout, $uibModal, $window, cleaner,
          http, related, routing, webStorage) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

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
          created: new Date(),
          starttime: null,
          endtime: null,
          title: '',
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
         *  Flag to enabled or disable drafts.
         *
         * @type {Boolean}
         */
        $scope.draftEnabled = false;

        /**
         * @memberOf ArticleCtrl
         *
         * @description
         *  The draft key.
         *
         * @type {String}
         */
        $scope.draftKey = 'article-draft';

        /**
         * @memberOf ArticleCtrl
         *
         * @description
         *  The timeout function for draft.
         *
         * @type {Function}
         */
        $scope.dtm = null;

        /**
         * @memberOf ArticleCtrl
         *
         * @description
         *  The related contents service
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

          // Check if item is new (created) or existing for use default value or not
          if (!$scope.data.item.pk_content) {
            $scope.item.with_comment = $scope.data.extra.comments_enabled ? 1 : 0;
          }

          related.init($scope);
          related.watch();
        };

        /**
         * @function checkDraft
         * @memberOf ArticleCtrl
         *
         * @description
         *   Checks if there is a draft from a previous article.
         */
        $scope.checkDraft = function() {
          if (!webStorage.session.has($scope.draftKey)) {
            return;
          }

          $uibModal.open({
            backdrop:    true,
            backdropClass: 'modal-backdrop-transparent',
            controller:  'YesNoModalCtrl',
            openedClass: 'modal-relative-open',
            templateUrl: 'modal-draft',
            windowClass: 'modal-right modal-small modal-top',
            resolve: {
              template: function() {
                return {};
              },
              yes: function() {
                return function(modalWindow) {
                  $scope.data.article = webStorage.session.get($scope.draftKey);

                  if ($scope.config.linkers.article) {
                    $scope.config.linkers.article.link(
                      $scope.data.article, $scope.article);
                    $scope.config.linkers.article.update();
                  } else {
                    $scope.article = $scope.data.article;
                  }

                  modalWindow.close({ response: true, success: true });

                  [ 'starttime', 'endtime', 'created' ].forEach(function(dateField) {
                    if ($scope.article[dateField]) {
                      $scope.article[dateField] = $window.moment($scope.article[dateField])
                        .format('YYYY-MM-DD HH:mm:ss');
                    }
                  });
                };
              },
              no: function() {
                return function(modalWindow) {
                  webStorage.session.remove($scope.draftKey);
                  modalWindow.close({ response: false, success: true });
                };
              }
            }
          });
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
         * @function preview
         * @memberOf ArticleCtrl
         *
         * @description
         *   Opens a modal with the preview of the article.
         *
         * @param {String} previewUrl    The URL to generate the preview.
         * @param {String} getPreviewUrl The URL to get the preview.
         */
        $scope.preview = function(previewUrl, getPreviewUrl) {
          $scope.flags.preview = true;

          var data = $scope.clean($scope.article);

          data = cleaner.clean(data);

          var postData = { article: data, locale: $scope.config.locale.selected };

          http.post(previewUrl, postData).then(function() {
            $uibModal.open({
              templateUrl: 'modal-preview',
              windowClass: 'modal-fullscreen',
              controller: 'ModalCtrl',
              resolve: {
                template: function() {
                  return {
                    src: $scope.routing.generate(getPreviewUrl)
                  };
                },
                success: function() {
                  return null;
                }
              }
            });

            $scope.disableFlags();
          }, function() {
            $scope.disableFlags();
          });
        };

        // Enable drafts after 5s to grant CKEditor initialization
        $timeout(function() {
          $scope.draftEnabled = true;
        }, 5000);

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
