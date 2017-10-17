(function () {
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
      '$controller', '$scope', '$timeout', '$uibModal', '$window', 'cleaner', 'http', 'linker', 'localizer', 'messenger', 'webStorage',
      function($controller, $scope, $timeout, $uibModal, $window, cleaner, http, linker, localizer, messenger, webStorage) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf ArticleCtrl
         *
         * @description
         *  The article object.
         *
         * @type {Object}
         */
        $scope.article = {
          body: '',
          params: {},
          summary: '',
          content_status: 0,
          created: new Date(),
          starttime: new Date()
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
        $scope.dtm  = null;

        /**
         * @function build
         * @memberOf ArticleCtrl
         *
         * @description
         *   Executes actions to adapt data from template to the webservice.
         */
        $scope.build = function() {
          // Convert metadata to an array
          if ($scope.data.article.metadata) {
            $scope.data.article.metadata =
              $scope.data.article.metadata.split(',');
          }

          var keys = [ 'img1', 'img2', 'fk_video', 'fk_video2','relatedFront',
            'relatedInner', 'relatedHome' ];

          for (var i = 0; i < keys.length; i++) {
            if (!$scope.data.extra[keys[i]]) {
              continue;
            }

            $scope.article[keys[i]] = $scope.data.extra[keys[i]];
          }

          keys = [ 'imageHome', 'withGallery', 'withGalleryInt',
            'withGalleryHome' ];

          for (var i = 0; i < keys.length; i++) {
            if (!$scope.data.extra[keys[i]]) {
              continue;
            }

            $scope.article.params[keys[i]] = $scope.data.extra[keys[i]];
          }
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

                  $scope.config.linkers.article.link(
                    $scope.data.article, $scope.article);
                  $scope.config.linkers.article.update();

                  modalWindow.close({ response: true, success: true });

                  if ($scope.article.starttime) {
                    $scope.article.starttime = $window.moment($scope.article.starttime)
                      .format('YYYY-MM-DD HH:mm:ss');
                  }

                  if ($scope.article.endtime) {
                    $scope.article.endtime = $window.moment($scope.article.endtime)
                      .format('YYYY-MM-DD HH:mm:ss');
                  }
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
         * @function clean
         * @memberOf ArticleCtrl
         *
         * @description
         *   Executes actions to adapt data from template to the webservice.
         */
        $scope.clean = function(article) {
          var data = angular.copy(article);

          if (angular.isArray(article.metadata)) {
            data.metadata = article.metadata.map(function(e) {
              return e.text;
            }).join(',');
          }

          var keys = [ 'img1', 'img2', 'fk_video', 'fk_video2' ];

          for (var i = 0; i < keys.length; i++) {
            if (!article[keys[i]]) {
              continue;
            }

            data[keys[i]] = article[keys[i]].pk_content;
          }

          keys = [ 'relatedFront', 'relatedInner', 'relatedHome' ];

          for (var i = 0; i < keys.length; i++) {
            if (!article[keys[i]]) {
              continue;
            }

            data[keys[i]] = [];

            for (var j = 0; j < article[keys[i]].length; j++) {
              data[keys[i]].push(article[keys[i]][j].pk_content);
            }
          }

          keys = [ 'imageHome', 'withGallery', 'withGalleryInt',
            'withGalleryHome' ];

          for (var i = 0; i < keys.length; i++) {
            if (!article.params[keys[i]]) {
              continue;
            }

            data.params[keys[i]] = article.params[keys[i]].pk_content;
          }

          return data;
        };

        /**
         * @function groupCategories
         * @memberOf ArticleListCtrl
         *
         * @description
         *   Groups categories in the ui-select.
         *
         * @param {Object} item The category to group.
         *
         * @return {String} The group name.
         */
        $scope.groupCategories = function(item) {
          var category = $scope.categories.filter(function(e) {
            return e.pk_content_category === item.fk_content_category;
          });

          if (category.length > 0 && category[0].pk_content_category) {
            return category[0].title;
          }

          return '';
        };

        /**
         * @function init
         * @memberOf ArticleListCtrl
         *
         * @description
         *   Initializes services and list articles.
         *
         * @param {Integer} id The article id when editing.
         */
        $scope.init = function(locale, id) {
          $scope.forcedLocale = locale;

          if (id) {
            $scope.draftKey = 'article-' + id + '-draft';
          }

          $scope.getArticle(id);
        };

        /**
         * @function getArticle
         * @memberOf ArticleCtrl
         *
         * @description
         *   Gets the article to show.
         *
         * @param {Integer} id The article id.
         */
        $scope.getArticle = function(id) {
          $scope.flags.loading = 1;

          var route = !id ? 'api_v1_backend_article_create' :
            { name: 'api_v1_backend_article_show', params: { id: id } };

          http.get(route).then(function(response) {
            $scope.data   = response.data;
            $scope.backup = { content_status: $scope.article.content_status };

            $scope.configure(response.data.extra);
            $scope.disableFlags();

            // Grant that article has all default values
            $scope.data.article =
              angular.merge($scope.data.article, $scope.article);


            // Load items
            $scope.article    = $scope.data.article;
            $scope.categories = $scope.data.extra.categories;

            var keys = [ 'relatedFront', 'relatedInner', 'relatedHome' ];

            for (var i = 0; i < keys.length; i++) {
              if ($scope.data.extra[keys[i]]) {
                $scope[keys[i]] = response.data.extra[keys[i]];
              }
            }

            $scope.build();

            if ($scope.config.multilanguage && $scope.config.locale) {
              $scope.localize();
            }

            $scope.checkDraft();
          }, $scope.errorCb);
        };

        /**
         * @function localize
         * @memberOf ArticleCtrl
         *
         * @description
         *   Configures the localization for the current form.
         */
        $scope.localize = function() {
          var lz   = localizer.get($scope.data.extra.options);
          var keys = [ 'relatedFront', 'relatedInner', 'relatedHome' ];

          // Localize original items
          $scope.article = lz.localize($scope.data.article,
            $scope.data.extra.keys, $scope.config.locale);
          $scope.config.linkers.article =
            linker.get($scope.data.extra.keys, $scope, true, keys);

          $scope.config.linkers.article.setKey($scope.config.locale);
          $scope.config.linkers.article.link($scope.data.article, $scope.article);

          $scope.categories = lz.localize($scope.data.extra.categories,
            [ 'title' ], $scope.config.locale);

          $scope.config.linkers.categories =
            linker.get($scope.data.extra.keys, $scope, true, keys);

          $scope.config.linkers.categories.setKey($scope.config.locale);
          $scope.config.linkers.categories.link($scope.data.extra.categories, $scope.categories);

          for (var i = 0; i < keys.length; i++) {
            if (!$scope[keys[i]]) {
              continue;
            }

            $scope.article[keys[i]] = lz.localize($scope.data.extra[keys[i]],
              [ 'title' ], $scope.config.locale);

            $scope.config.linkers[keys[i]] = linker.get([ 'title' ], $scope);

            $scope.config.linkers[keys[i]].setKey($scope.config.locale);
            $scope.config.linkers[keys[i]].link($scope.data.article[keys[i]],
              $scope.article[keys[i]]);
          }
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

          var data = angular.copy($scope.article);

          if (angular.isArray(data.metadata)) {
            data.metadata = data.metadata.map(function(e) {
              return e.text;
            }).join(',');
          }

          var data = { 'article': data };

          http.post(previewUrl, data).success(function() {
            $uibModal.open({
              templateUrl: 'modal-preview',
              windowClass: 'modal-fullscreen',
              controller: 'modalCtrl',
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
          }).error(function() {
            $scope.disableFlags();
          });
        };

        /**
         * @function save
         * @memberOf ArticleCtrl
         *
         * @description
         *   Saves a new article.
         */
        $scope.save = function() {
          if ($scope.articleForm.$invalid) {
            $scope.showRequired = true;
            return;
          }

          $scope.flags.saving = true;

          var data = $scope.clean($scope.data.article);

          data = cleaner.clean(data);

          /**
           * Callback executed when article is saved/updated successfully.
           *
           * @param {Object} response The response object.
           */
          var successCb = function(response) {
            $scope.disableFlags();
            //webStorage.session.remove($scope.draftKey);

            if (response.status === 201) {
              $window.location.href = response.headers().location;
            }

            $scope.articleForm.$setPristine(true);
            messenger.post(response.data);
            $scope.backup.content_status = $scope.article.content_status;
          };

          if (!$scope.article.pk_article) {
            http.post('backend_ws_article_save', data)
              .then(successCb, $scope.errorCb);

            return;
          }

          http.put({
            name: 'backend_ws_article_update',
            params: { id: $scope.article.pk_article }
          }, data).then(successCb, $scope.errorCb);
        };

        // Update footers when photos change
        $scope.$watch('[ article.img1, article.img2, article.params.imageHome ]',
          function(nv, ov) {
            if (angular.equals(nv, ov)) {
              return;
            }

            for (var i = 0; i < nv.length; i++) {
              var footer = 'img' + (i + 1) + '_footer';
              var model  = $scope.article;

              if (i === 2) {
                footer = 'imageHomeFooter';
                model  = $scope.article.params;
              }

              if (angular.isUndefined(model[footer]) || model[footer] === null ||
                  (ov && ov[i] && model[footer] === ov[i].description)) {
                model[footer] = nv[i].description;
              }
            }

            if ($scope.articleForm.$dirty && (!$scope.article.img2) ||
                angular.equals($scope.article.img2, ov[1])) {
              $scope.article.img2 = $scope.article.img1;
            }
          }, true);

        // Updates footers when videos changes
        $scope.$watch('[ article.fk_video, article.fk_video2 ]',
          function(nv, ov) {
            if (angular.equals(nv, ov)) {
              return;
            }

            for (var i = 0; i < nv.length; i++) {
              var footer = 'footer_video';
              var model  = $scope.article;

              if (i > 0) {
                footer = 'footer_video2';
              }

              if (angular.isUndefined(model[footer]) || model[footer] === null ||
                  (ov && ov[i] && model[footer] === ov[i].description)) {
                model[footer] = nv[i].description;
              }
            }

            if ($scope.articleForm.$dirty && (!$scope.article.fk_video2) ||
                angular.equals($scope.article.fk_video2, ov[1])) {
              $scope.article.fk_video2 = $scope.article.fk_video;
            }
          }, true);

        // Sets relatedInner equals to relatedFront
        $scope.$watch('article.relatedFront', function(nv, ov) {
          if ((!ov && $scope.article.relatedInner) ||
              angular.equals(ov, $scope.article.relatedInner)) {
            $scope.article.relatedInner = angular.copy(nv);
          }
        }, true);

        // TODO: Remove when no target="_blank" in URI for external
        $scope.$watch('article.uri', function(nv, ov) {
          if (nv !== ov) {
            $scope.article.uri = $scope.article.uri
              .replace('" target="_blank', '');
          }
        }, true);

        // Saves a draft 2.5s after the last change
        $scope.$watch('article', function(nv, ov) {
          if (!nv || ov === nv) {
            return;
          }

          // Show a message when leaving before saving
          $($window).bind('beforeunload', function() {
            if ($scope.articleForm.$dirty){
              return $window.leaveMessage;
            }
          });

          $scope.articleForm.$setDirty(true);

          if ($scope.draftEnabled) {
            $scope.draftSaved = null;

            if ($scope.dtm) {
              $timeout.cancel($scope.dtm);
            }

            $scope.dtm = $timeout(function() {
              webStorage.session.set($scope.draftKey, $scope.data.article);

              $scope.draftSaved = $window.moment().format('HH:mm');
            }, 2500);
          }
        }, true);

        // Update title_int when title changes
        $scope.$watch('article.title', function(nv, ov) {
          if (nv && (!$scope.article.title_int ||
              ov === $scope.article.title_int)) {
            $scope.article.title_int = nv;
          }
        }, true);

        // Update metadata when title or category change
        $scope.$watch('[ article.title, article.category ]', function(nv, ov) {
          if (($scope.article.metadata && $scope.article.metadata.length > 0) ||
              !nv || nv === ov) {
            return;
          }

          var title    = $scope.article.title ? $scope.article.title : '';
          var category = '';
          var data     = title + ' ' + category;

          // Get category name from category id
          if ($scope.article.category) {
            var categories = $scope.data.extra.categories.filter(function(e) {
              return e.pk_content_category ===
                $scope.article.pk_fk_content_category;
            });

            if (categories.length > 0) {
              category = categories[0].title;
            }
          }

          if (!$scope.config.multilanguage) {
            if ($scope.mtm) {
              $timeout.cancel($scope.mtm);
            }

            $scope.mtm = $timeout(function() {
              http.get({
                name: 'admin_utils_calculate_tags',
                params: { data: data }
              }).then(function(response) {
                $scope.article.metadata = response.data.split(',');
              });
            }, 2500);
          }
        });

        // Enable drafts after 5s to grant CKEditor initialization
        $timeout(function() {
          $scope.draftEnabled = true;
        }, 5000);
      }
    ]);
})();
