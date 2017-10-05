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
         * @function checkDraft
         * @memberOf ArticleCtrl
         *
         * @description
         *   Checks if there is a draft from a previous article.
         */
        $scope.checkDraft = function() {
          var key = 'article-draft';

          if ($scope.article && $scope.article.pk_article) {
            key = 'article-' + $scope.article.pk_article + '-draft';
          }

          if (!webStorage.session.has(key)) {
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
                  var draft =  webStorage.session.get(key);

                  for (var name in draft) {
                    if (name === 'article') {
                      $scope.data.article = draft[name];

                      $scope.config.linkers.il.link(
                        $scope.data.article, $scope.article);

                      $scope.config.linkers.il.update();

                      continue;
                    }

                    $scope[name] = draft[name];
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
                  webStorage.session.remove(key);
                  modalWindow.close({ response: false, success: true });
                };
              }
            }
          });
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
        $scope.init = function(id) {
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
          $scope.loading = 1;

          var route = !id ? 'api_v1_backend_article_create' :
            { name: 'api_v1_backend_article_show', params: { id: id } };

          http.get(route).then(function(response) {
            $scope.loading = 0;
            $scope.data    = response.data;

            // Duplicate default article when creating
            if (!response.data.article) {
              response.data.article = angular.copy($scope.article);
            }

            if (response.data.article) {
              $scope.backup = { content_status: $scope.article.content_status };

              // Convert metadata to an array
              if (response.data.article.metadata) {
                response.data.article.metadata =
                  response.data.article.metadata.split(',');
              }
            }

            // Load items
            $scope.article    = response.data.article;
            $scope.categories = response.data.extra.categories;

            // Configure the form
            if ($scope.config.multilanguage === null) {
              $scope.config.multilanguage = response.data.extra.multilanguage;
            }

            if ($scope.config.locale === null) {
              $scope.config.locale = response.data.extra.locale;
            }

            if ($scope.config.multilanguage && $scope.config.locale) {
              $scope.config.linkers.il =
                linker.get(response.data.extra.keys, $scope, true);
              $scope.config.linkers.cl =
                linker.get([ 'title' ], $scope);

              var lz = localizer.get($scope.data.extra.options);

              $scope.categories =
                lz.localize($scope.categories, $scope.locale);

              $scope.article =
                lz.localize(response.data.article, $scope.locale);

              $scope.config.linkers.cl.setKey($scope.config.locale);
              $scope.config.linkers.il.setKey($scope.config.locale);

              $scope.config.linkers.cl.link(
                $scope.data.extra.categories, $scope.categories);
              $scope.config.linkers.il.link(
                $scope.data.article, $scope.article);
            }

            $scope.checkDraft();
          }, function(response) {
            $scope.loading = 0;
            messenger.post(response.data);
          });
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
          $scope.previewLoading = true;

          var data = angular.copy($scope.article);
          data.metadata = data.metadata.map(function(e) {
            return e.text;
          }).join(',');

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

            $scope.previewLoading = false;
          }).error(function() {
            $scope.previewLoading = false;
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

          var data = angular.copy($scope.article);
          data.metadata = data.metadata.map(function(e) {
            return e.text;
          }).join(',');

          $scope.saving = true;

          http.post('backend_ws_article_save', data)
            .then(function(response) {
              $scope.saving = false;
              $scope.articleForm.$setPristine(true);

              if (response.status === 201) {
                webStorage.session.remove('article-draft');
                $window.location.href = response.headers().location;
              }
            }, function(response) {
              $scope.saving = false;
              messenger.post(response.data);
            });
        };

        /**
         * @function update
         * @memberOf ArticleCtrl
         *
         * @description
         *   Updates an article.
         */
        $scope.update = function() {
          if ($scope.articleForm.$invalid) {
            $scope.showRequired = true;
            return;
          }

          $scope.saving = true;

          var data = angular.copy($scope.article);
          data.metadata = data.metadata.map(function(e) {
            return e.text;
          }).join(',');

          var route = {
            name: 'backend_ws_article_update',
            params: { id: $scope.article.pk_article }
          };

          http.put(route, data)
            .then(function(response) {
              $scope.saving = false;
              webStorage.session.remove('article-' +
                  $scope.article.pk_article + '-draft');
              $scope.articleForm.$setPristine(true);
              messenger.post(response.data);
              $scope.backup.content_status = $scope.article.content_status;
            }, function(response) {
              $scope.saving = false;
              messenger.post(response.data);
            });
        };

        // Updates scope when photo1 changes.
        $scope.$watch('photo1', function(nv, ov) {
          // Remove image
          if (!nv) {
            $scope.article.img1 = null;

            // If photo 2  = photo1, remove photo2 too
            if (angular.equals($scope.photo2, ov)) {
              $scope.photo2 = null;
            }

            delete $scope.article.img1_footer;

            return;
          }

          $scope.article.img1 = nv.id;

          // Update img1_footer if empty or equals to old description
          if (angular.isUndefined($scope.article.img1_footer) ||
              $scope.article.img1_footer === null ||
              (ov && $scope.article.img1_footer === ov.description)) {
            $scope.article.img1_footer = nv.description;
          }

          // Set inner image if empty or equals to old photo1
          if ($scope.articleForm.$dirty &&
              (!ov && (angular.isUndefined($scope.photo2) || !$scope.photo2)) ||
              (ov &&angular.equals($scope.photo2, ov))) {

            $scope.photo2 = nv;
          }
        }, true);

        // Updates scope when photo2 changes.
        $scope.$watch('photo2', function(nv, ov) {
          // Remove image
          if (!nv) {
            $scope.article.img2 = null;

            delete $scope.article.img2_footer;

            return;
          }

          $scope.article.img2 = $scope.photo2.id;

          if (angular.isUndefined($scope.article.img2_footer) ||
              $scope.article.img2_footer === null ||
              (ov && $scope.article.img2_footer === ov.description)) {
            $scope.article.img2_footer = $scope.photo2.description;
          }
        }, true);

        //Updates scope when photo3 changes.
        $scope.$watch('photo3', function(nv, ov) {
          // Remove image
          if (!nv) {

            $scope.article.params.imageHome = null;

            delete $scope.article.params.imageHomeFooter;

            return;
          }

          $scope.article.params.imageHome = $scope.photo3.id;

          if (angular.isUndefined($scope.article.params.imageHomeFooter) ||
              $scope.article.params.imageHomeFooter === null ||
              (ov && ov.description === $scope.article.params.imageHomeFooter)) {
            $scope.article.params.imageHomeFooter = $scope.photo3.description;
          }
        }, true);

        // Updates scope when video1 changes.
        $scope.$watch('video1', function(nv, ov) {
          $scope.article.fk_video     = null;
          $scope.article.footer_video = null;

          if ($scope.video1) {
            $scope.article.fk_video     = $scope.video1.id;
            $scope.article.footer_video = $scope.video1.description;

            // Set inner video if empty
            if (angular.isUndefined($scope.video2) && nv !== ov) {
              $scope.video2 = $scope.video1;
            }
          }
        }, true);

        // Updates scope when video2 changes.
        $scope.$watch('video2', function() {
          $scope.article.fk_video2     = null;
          $scope.article.footer_video2 = null;

          if ($scope.video2) {
            $scope.article.fk_video2     = $scope.video2.id;
            $scope.article.footer_video2 = $scope.video2.description;
          }
        }, true);

        // Updates scope when relatedInFrontpage changes.
        $scope.$watch('relatedInFrontpage', function(nv, ov) {
          // Set inner if creating and empty or when old value in front is
          // equals to current value in inner
          if ((!$scope.article.pk_article && !$scope.relatedInInner) || (ov &&
                angular.equals(cleaner.clean($scope.relatedInInner),
                  cleaner.clean(ov)))) {
            $scope.relatedInInner = angular.copy(nv);
          }

          var items                   = [];
          $scope.article.relatedFront = [];

          if (nv instanceof Array) {
            for (var i = 0; i < nv.length; i++) {
              items.push({ id: nv[i].id, position: i, content_type: nv[i].content_type_name });
            }
          }

          $scope.article.relatedFront = angular.toJson(items);
        }, true);

        // Updates scope when relatedInInner changes.
        $scope.$watch('relatedInInner', function(nv) {
          var items                   = [];
          $scope.article.relatedInner = [];

          if (nv instanceof Array) {
            for (var i = 0; i < nv.length; i++) {
              items.push({ id: nv[i].id, position: i, content_type: nv[i].content_type_name });
            }
          }

          $scope.article.relatedInner = angular.toJson(items);
        }, true);

        // Updates scope when relatedInHome changes.
        $scope.$watch('relatedInHome', function(nv) {
          var items                  = [];
          $scope.article.relatedHome = [];

          if (nv instanceof Array) {
            for (var i = 0; i < nv.length; i++) {
              items.push({ id: nv[i].id, position: i, content_type: nv[i].content_type_name });
            }
          }

          $scope.article.relatedHome = angular.toJson(items);
        }, true);

        // TODO: Remove when no target="_blank" in URI for external
        $scope.$watch('article.uri', function(nv, ov) {
          if (nv !== ov) {
            $scope.article.uri = $scope.article.uri
              .replace('" target="_blank', '');
          }
        }, true);

        // Updates the model when galleryForFrontpage changes.
        $scope.$watch('galleryForFrontpage', function(nv) {
          delete $scope.article.withGallery;

          if (nv) {
            $scope.article.params.withGallery = nv.id;
          }
        }, true);

        // Updates the model when galleryForInner changes.
        $scope.$watch('galleryForInner', function(nv) {
          delete $scope.article.params.withGalleryInt;

          if (nv) {
            $scope.article.params.withGalleryInt = nv.id;
          }
        }, true);

        // Updates the model when galleryForHome changes.
        $scope.$watch('galleryForHome', function(nv) {
          delete $scope.article.params.withGalleryHome;

          if (nv) {
            $scope.article.params.withGalleryHome = nv.id;
          }
        }, true);


        $scope.dtm  = null;

        // Saves a draft 1s after the last change
        $scope.$watch('[article, photo1, photo2, photo3, video1, video2,' +
          'galleryForFrontpage, galleryForInner, galleryForHome]',
          function(nv, ov) {
            if (!nv || ov === nv || (ov[0] && !ov[0].pk_article &&
                  nv[0].pk_article)) {
              return;
            }

            // Show a message when leaving before saving
            $($window).bind('beforeunload', function() {
              if ($scope.articleForm.$dirty){
                return $window.leaveMessage;
              }
            });

            var key = 'article-draft';
            $scope.draftSaved = null;

            if (ov && nv !== ov && $scope.draftEnabled) {
              if ($scope.article.pk_article) {
                key = 'article-' + $scope.article.pk_article + '-draft';
              }

              webStorage.session.set(key, {
                article:             $scope.data.article,
                photo1:              $scope.photo1,
                photo2:              $scope.photo2,
                photo3:              $scope.photo3,
                video1:              $scope.video1,
                video2:              $scope.video2,
                relatedInHome:       $scope.relatedInHome,
                relatedInInner:      $scope.relatedInInner,
                relatedInFrontpage:  $scope.relatedInFrontpage,
                galleryForFrontpage: $scope.galleryForFrontpage,
                galleryForInner:     $scope.galleryForInner,
                galleryForHome:      $scope.galleryForHome,
              });

              // Cancel draft save
              if ($scope.dtm) {
                $timeout.cancel($scope.dtm);
              }

              $scope.dtm = $timeout(function() {
                $scope.draftSaved = $window.moment().format('HH:mm');
              }, 2500);
            }

            $scope.articleForm.$setDirty(true);
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
        });

        // Enable drafts after 5s to grant CKEditor initialization
        $timeout(function() {
          $scope.draftEnabled = true;
        }, 5000);
      }
    ]);
})();
