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
     * @requires Editor
     * @requires http
     * @requires messenger
     * @requires routing
     * @requires webStorage
     *
     * @description
     *   Provides actions to edit, save and update articles.
     */
    .controller('ArticleCtrl', [
      '$controller', '$interval', '$scope', '$timeout', '$uibModal', '$window', 'Editor', 'http', 'messenger', 'routing', 'webStorage',
      function($controller, $interval, $scope, $timeout, $uibModal, $window, Editor, http, messenger, routing, webStorage) {
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
        $scope.article = { params: {} };

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
         * @function getArticle
         * @memberOf ArticleCtrl
         *
         * @description
         *   Gets the article to show.
         *
         * @param {Integer} id The article id.
         */
        $scope.getArticle = function(id) {
          $scope.loading = true;

          var route = {
            name:   'backend_ws_article_show',
            params: { id: id }
          };

          http.get(route).then(function(response) {
            for (var key in response.data) {
              $scope[key] = response.data[key];
            }

            if ($scope.article.metadata) {
              $scope.article.metadata = $scope.article.metadata.split(',');
            }

            $scope.checkDraft();
            $scope.loading = false;
          }, function(response) {
            $scope.loading = false;
            $scope.error   = true;
            messenger.post(response.data);
          });
        };

        /**
         * @function getCategoryName
         * @memberOf ArticleCtrl
         *
         * @description
         *   Returns the category name given a category id.
         *
         * @param {Integer} id The category id.
         *
         * @return {String} The category name.
         */
        $scope.getCategoryName = function(id) {
          return $scope.categories.filter(function(e) {
            return parseInt(e.id) === parseInt(id);
          }).map(function (e) {
            return e.name;
          }).join('');
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
                    src: routing.generate(getPreviewUrl)
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
         * @function removeAlbum
         * @memberOf ArticleCtrl
         *
         * Removes an album.
         *
         * @param {String} from The album name in the current scope.
         */
        $scope.removeAlbum = function(from) {
          delete $scope[from];
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
            }, function(response) {
              $scope.saving = false;
              messenger.post(response.data);
            });
        };

        // Updates scope when photo1 changes.
        $scope.$watch('photo1', function(nv, ov) {
          // Reset image if is not set
          if (ov && !nv) {
            $scope.article.img1 = null;
            delete $scope.article.img1_footer;
            return;
          }

          if ($scope.photo1) {
            $scope.article.img1 = $scope.photo1.id;
            if ((angular.isUndefined($scope.article.img1_footer) &&
                  angular.isUndefined(ov)) ||
                (!angular.isUndefined(ov) && nv.id !== ov.id &&
                  ov.description === $scope.article.img1_footer)) {
              $scope.article.img1_footer = $scope.photo1.description;
            }

            // Set inner image if empty
            if (angular.isUndefined($scope.photo2) &&
                (!angular.isUndefined(ov) &&
                  nv !== ov ||
                  angular.isUndefined($scope.article.id))) {
              delete $scope.article.img2_footer;
              $scope.photo2 = $scope.photo1;
            }
          }
        }, true);

        // Updates scope when photo2 changes.
        $scope.$watch('photo2', function(nv, ov) {
          // Reset image if is not set
          if (ov && !nv) {
            $scope.article.img2 = null;
            delete $scope.article.img2_footer;
            return;
          }

          if ($scope.photo2) {
            $scope.article.img2 = $scope.photo2.id;
            if ((angular.isUndefined($scope.article.img2_footer) &&
                  angular.isUndefined(ov)) ||
                (!angular.isUndefined(ov) && nv.id !== ov.id &&
                  ov.description === $scope.article.img2_footer)) {
              $scope.article.img2_footer = $scope.photo2.description;
            }
          }
        }, true);

        //Updates scope when photo3 changes.
        $scope.$watch('photo3', function(nv, ov) {
          // Reset image if is not set
          if (ov && !nv) {
            $scope.article.imageHome = null;
            delete $scope.article.params.imageHomeFooter;
            return;
          }

          if ($scope.photo3) {
            $scope.article.params.imageHome = $scope.photo3.id;
            if ((angular.isUndefined($scope.article.params.imageHomeFooter) &&
                  angular.isUndefined(ov)) ||
                (!angular.isUndefined(ov) && nv.id !== ov.id &&
                  ov.description === $scope.article.imageHomeFooter)) {
              $scope.article.params.imageHomeFooter = $scope.photo3.description;
            }
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
          // Set inner related if empty or equal to front
          if ((!$scope.relatedInInner ||
              $scope.article.relatedInner === $scope.article.relatedFront) &&
              nv !== ov) {
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
            if (!nv || ov === nv || (!ov.pk_article && nv.pk_article)) {
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
                article:             $scope.article,
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
                $scope.draftSaved = $window.draftSavedMsg +
                  $window.moment().format('HH:mm');

              }, 2500);
            }

            $scope.articleForm.$setDirty(true);
          }, true);

        // Update title_int when title changes
        $scope.$watch('article.title', function(nv) {
          if (nv && (!$scope.article.title_int ||
              nv.substr(0, $scope.article.title_int.length) ===
              $scope.article.title_int)) {
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

          if ($scope.article.category) {
            category = $scope.getCategoryName($scope.article.category);
          }

          var data  = title + ' ' + category;
          var route = {
            name:   'admin_utils_calculate_tags',
            params: { data: data }
          };

          if ($scope.mtm) {
            $timeout.cancel($scope.mtm);
          }

          $scope.mtm = $timeout(function() {
            http.get(route).then(function(response) {
              $scope.article.metadata = response.data.split(',');
            });
          }, 500);
        });

        // Enable drafts after 5s to grant CKEditor initialization
        $timeout(function() {
          $scope.draftEnabled = true;
        }, 5000);
      }
    ]);
})();
