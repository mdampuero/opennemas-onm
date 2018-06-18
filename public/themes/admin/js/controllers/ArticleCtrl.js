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
      '$controller', '$scope', '$timeout', '$uibModal', '$window', 'cleaner', 'http', 'linker', 'localizer', 'messenger', 'webStorage',
      function($controller, $scope, $timeout, $uibModal, $window, cleaner,
          http, linker, localizer, messenger, webStorage) {
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
        $scope.dtm = null;

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

          if ($scope.data.article.subscriptions) {
            for (var i = 0; i < $scope.data.article.subscriptions.length; i++) {
              $scope.data.article.subscriptions[i] =
                parseInt($scope.data.article.subscriptions[i]);
            }
          }

          var keys = [
            'img1', 'img2', 'fk_video', 'fk_video2', 'relatedFront',
            'relatedInner', 'relatedHome'
          ];

          for (var i = 0; i < keys.length; i++) {
            if (!$scope.data.extra[keys[i]]) {
              $scope.article[keys[i]] = null;
              continue;
            }

            $scope.article[keys[i]] = $scope.data.extra[keys[i]];
          }

          keys = [
            'imageHome', 'withGallery', 'withGalleryInt',
            'withGalleryHome'
          ];

          for (var j = 0; j < keys.length; j++) {
            if (!$scope.data.extra[keys[j]]) {
              continue;
            }

            $scope.article.params[keys[j]] = $scope.data.extra[keys[j]];
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

                  if ($scope.config.linkers.article) {
                    $scope.config.linkers.article.link(
                      $scope.data.article, $scope.article);
                    $scope.config.linkers.article.update();
                  } else {
                    $scope.article = $scope.data.article;
                  }

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
        $scope.clean = function(article, preview) {
          var data = angular.copy(article);

          if (angular.isArray(article.metadata)) {
            data.metadata = article.metadata.map(function(e) {
              return e.text;
            }).join(',');
          }

          var keys = [ 'img1', 'img2', 'fk_video', 'fk_video2' ];

          for (var k = 0; k < keys.length; k++) {
            if (!article[keys[k]]) {
              continue;
            }

            data[keys[k]] = article[keys[k]].pk_content;
          }

          keys = [ 'relatedFront', 'relatedInner', 'relatedHome' ];

          for (var l = 0; l < keys.length; l++) {
            if (!article[keys[l]]) {
              continue;
            }

            data[keys[l]] = [];

            for (var m = 0; m < article[keys[l]].length; m++) {
              var item = article[keys[l]][m].pk_content;

              if (preview) {
                item = {
                  id: article[keys[l]][m].pk_content,
                  type: article[keys[l]][m].content_type_name
                };
              }

              data[keys[l]].push(item);
            }
          }

          keys = [
            'imageHome', 'withGallery', 'withGalleryInt',
            'withGalleryHome'
          ];

          for (var n = 0; n < keys.length; n++) {
            if (!article.params[keys[n]]) {
              continue;
            }

            data.params[keys[n]] = article.params[keys[n]].pk_content;
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
            $scope.backup = { content_status: 0 };

            $scope.configure(response.data.extra);
            $scope.disableFlags();

            if ($scope.data.article) {
              $scope.backup.content_status = $scope.data.article.content_status;
            }

            // Grant that article has all default values
            $scope.data.article =
              angular.extend($scope.article, $scope.data.article);

            if (!('with_comment' in $scope.data.article)) {
              $scope.data.article.with_comment =
                $scope.data.extra.with_comment ? 1 : 0;
            }

            // Load items
            $scope.article      = $scope.data.article;
            $scope.categories   = $scope.data.extra.categories;
            $scope.fieldsByModule = $scope.data.extra.moduleFields;

            $scope.build();

            if ($scope.config.multilanguage && $scope.config.locale) {
              $scope.localize();
            }

            $scope.checkDraft();
          }, $scope.errorCb);
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
            linker.get($scope.data.extra.keys, $scope, false, keys);

          $scope.config.linkers.categories.setKey($scope.config.locale);
          $scope.config.linkers.categories.link($scope.data.extra.categories, $scope.categories);

          for (var i = 0; i < keys.length; i++) {
            if (!$scope.article[keys[i]]) {
              continue;
            }

            $scope.data.article[keys[i]] = lz.localize($scope.data.extra[keys[i]],
              [ 'title' ], $scope.config.locale);
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

          var data = $scope.clean($scope.article, true);

          data = cleaner.clean(data);

          if (angular.isArray(data.metadata)) {
            data.metadata = data.metadata.map(function(e) {
              return e.text;
            }).join(',');
          }

          var postData = { article: data, locale: $scope.config.locale };

          http.post(previewUrl, postData).success(function() {
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
          if ($scope.articleForm.$invalid || !$scope.data.article.pk_fk_content_category) {
            $scope.showRequired = true;
            return;
          }

          $scope.articleForm.$setPristine(true);

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
            webStorage.session.remove($scope.draftKey);

            if (response.status === 201) {
              $window.location.href = response.headers().location;
            }

            messenger.post(response.data);
            $scope.backup.content_status = $scope.article.content_status;
          };

          /**
           * Callback executed when article is saved/updated fails.
           *
           * @param {Object} response The response object.
           */
          var saveErrorCb = function(response) {
            $scope.disableFlags();
            $scope.errorCb(response);
          };

          if (!$scope.article.pk_article) {
            var route = { name: 'backend_ws_article_save' };

            if ($scope.config.multilanguage) {
              route.params = { locale: $scope.config.locale };
            }

            http.post(route, data).then(successCb, saveErrorCb);
            return;
          }

          http.put({
            name: 'backend_ws_article_update',
            params: { id: $scope.article.pk_article }
          }, data).then(successCb, saveErrorCb);
        };

        /**
         * @function translate
         * @memberOf ArticleCtrl
         *
         * @description
         *   Shows a modal to translate a content automatically.
         *
         * @param {String} to     The locale to translate to.
         * @param {Object} config The locale-related configuration.
         *
         * @return {type} description
         */
        $scope.translate = function(to, configParam) {
          var config = {
            translateFrom:  $scope.data.extra.locale,
            translateTo: to,
            locales: configParam.locales,
            translators: configParam.translators,
            translatorSelected: 0,
          };

          // Pick the default translator
          config.translators.forEach(function(el, index) {
            if (el.from === config.translateFrom &&
              el.to === config.translateTo &&
              el.default === true || el.default === 'true') {
              config.translatorSelected = index;
            }
          });

          // Raise a modal to indicate that background translation is being executed
          $uibModal.open({
            backdrop: 'static',
            keyboard: false,
            backdropClass: 'modal-backdrop-dark',
            controller:  'BackgroundTaskModalCtrl',
            openedClass: 'modal-relative-open',
            templateUrl: 'modal-translate',
            resolve: {
              template: function() {
                return {
                  config: config,
                  translating: false,
                };
              },
              callback: function() {
                return function(modal, template) {
                  var translator = config.translators[config.translatorSelected];

                  // If no default translator dont call the server
                  if (!translator) {
                    return;
                  }

                  template.translating = true;
                  template.translation_done = false;

                  var params = {
                    data: {},
                    from: translator.from,
                    to: translator.to,
                    translator: config.translatorSelected,
                  };

                  for (var i = 0; i < $scope.data.extra.keys.length; i++) {
                    var key = $scope.data.extra.keys[i];

                    if ($scope.data.article[key] &&
                        angular.isObject($scope.data.article[key]) &&
                        $scope.data.article[key][params.from]) {
                      params.data[key] = $scope.data.article[key][params.from];
                    }
                  }

                  template.translating = true;
                  template.translation_done = false;

                  http.post('api_v1_backend_tools_translate_string', params)
                    .then(function(response) {
                      for (var i = 0; i < $scope.data.extra.keys.length; i++) {
                        var key = $scope.data.extra.keys[i];

                        $scope.article[key] = response.data[key];
                      }

                      template.translating = false;
                      template.translation_done = true;
                    }, function(response) {
                      var message = {
                        id: new Date().getTime(),
                        message: 'Unable to translate contents. Please check your configuration.',
                        type: 'error'
                      };

                      modal.close({ response: true, error: true });
                    });
                };
              }
            }
          });
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

              if (angular.isObject(nv[i]) &&
                  (angular.isUndefined(model[footer]) ||
                  model[footer] === null || ov && ov[i] &&
                  model[footer] === ov[i].description)) {
                model[footer] = nv[i].description;
              }

              if (!nv[i]) {
                model[footer] = null;
              }
            }

            if ($scope.articleForm.$dirty &&
                (!nv[1] && angular.equals(nv[1], ov[1]) ||
                angular.equals(nv[1], ov[0]))) {
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
              var footer = 'footer_video' + (i + 1);
              var model  = $scope.article;

              if (angular.isObject(nv[i]) &&
                (angular.isUndefined(model[footer]) ||
                  model[footer] === null || ov && ov[i] &&
                  model[footer] === ov[i].description)) {
                model[footer] = nv[i].description;
              }

              if (!nv[i]) {
                model[footer] = null;
              }
            }

            if ($scope.articleForm.$dirty &&
                (!nv[1] && angular.equals(nv[1], ov[1]) ||
                angular.equals(nv[1], ov[0]))) {
              $scope.article.fk_video2 = $scope.article.fk_video;
            }
          }, true);

        // Sets relatedInner equals to relatedFront
        $scope.$watch('data.article.relatedFront', function(nv, ov) {
          if ($scope.data && (!$scope.data.article.relatedInner ||
              angular.equals(ov, $scope.data.article.relatedInner))) {
            $scope.data.article.relatedInner = angular.copy(nv);
          }
        }, true);

        // TODO: Remove when no target="_blank" in URI for external
        $scope.$watch('article.uri', function(nv, ov) {
          if (nv !== ov) {
            if (typeof $scope.article.uri === 'string') {
              $scope.article.uri = $scope.article.uri
                .replace('" target="_blank', '');
            }
          }
        }, true);

        // Saves a draft 2.5s after the last change
        $scope.$watch('article', function(nv, ov) {
          if (!nv || ov === nv) {
            return;
          }

          // Show a message when leaving before saving
          $($window).bind('beforeunload', function() {
            if ($scope.articleForm.$dirty) {
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
          if (!nv) {
            return;
          }

          if (nv && (!$scope.article.title_int ||
              ov === $scope.article.title_int)) {
            $scope.article.title_int = nv;
          }

          if (!$scope.article.slug || $scope.article.slug === '') {
            if ($scope.tm) {
              $timeout.cancel($scope.tm);
            }

            $scope.tm = $timeout(function() {
              $scope.getSlug(nv, function(response) {
                $scope.article.slug = response.data.slug;
              });
            }, 2500);
          }
        }, true);

        // Update metadata when title or category change
        $scope.$watch('[ article.title, article.category ]', function(nv, ov) {
          if ($scope.article.metadata && $scope.article.metadata.length > 0 ||
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

        // Shows a modal window to translate content automatically
        $scope.$watch('config.locale', function(nv, ov) {
          if (!nv || nv === ov ||
            $scope.isTranslated($scope.data.article,
              $scope.data.extra.keys, nv)) {
            return;
          }

          // Filter for selected locale and translated in original language
          var translators = $scope.config.translators.filter(function(e) {
            return e.to === nv && $scope.isTranslated($scope.data.article,
              $scope.data.extra.keys, e.from);
          });

          if (translators.length === 0) {
            return;
          }

          var config = {
            locales: $scope.data.extra.options.available,
            translators: translators
          };

          $scope.translate(nv, config);
        }, true);

        // Enable drafts after 5s to grant CKEditor initialization
        $timeout(function() {
          $scope.draftEnabled = true;
        }, 5000);
      }
    ]);
})();
