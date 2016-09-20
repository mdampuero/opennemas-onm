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

          if (!webStorage.has(key)) {
            return;
          }

          $uibModal.open({
            templateUrl: 'modal-draft',
            controller: 'YesNoModalCtrl',
            resolve: {
              template: function() {
                return {};
              },
              yes: function() {
                return function(modalWindow) {
                  var draft =  webStorage.get(key);

                  for (var name in draft) {
                    $scope[name] = draft[name];
                  }

                  modalWindow.close({ response: true, success: true });

                  // Force Editor update
                  Editor.get('summary').setData($scope.article.summary);
                  Editor.get('body').setData($scope.article.body);

                  // Force metadata
                  for (var tag in $scope.article.metadata.split(',')) {
                    $('#metadata').tagsinput('add', tag);
                  }

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
                  webStorage.local.remove(key);
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
          var route = {
            name:   'backend_ws_article_show',
            params: { id: id }
          };

          http.get(route).then(function(response) {
            for (var key in response.data) {
              $scope[key] = response.data[key];
            }

            // Force Editor update
            Editor.get('summary').setData($scope.article.summary);
            Editor.get('body').setData($scope.article.body);

            if ($scope.article.metadata) {
              var tags = $scope.article.metadata.split(',');
              for (var i = 0; i < tags.length; i++) {
                $('#metadata').tagsinput('add', tags[i]);
              }
            }

            $scope.checkDraft();
          }, function(response) {
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
          $scope.loading = true;

          // Force Editor update
          Editor.get('body').updateElement();
          Editor.get('summary').updateElement();

          var data = { 'article': $scope.article };

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

            $scope.loading = false;
          }).error(function() {
            $scope.loading = false;
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
          $scope.saving = true;

          http.post('backend_ws_article_save', $scope.article)
            .then(function(response) {
              $scope.saving = false;

              if (response.status === 201) {
                webStorage.local.remove('article-draft');
                $scope.unsaved = false;
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
          $scope.saving = true;

          var route = {
            name: 'backend_ws_article_update',
            params: { id: $scope.article.pk_article }
          };

          http.put(route, $scope.article)
            .then(function(response) {
              $scope.saving = false;
              webStorage.local.remove('article-' +
                  $scope.article.pk_article + '-draft');
              $scope.unsaved = false;
              messenger.post(response.data);
            }, function(response) {
              $scope.saving = false;
              messenger.post(response.data);
            });
        };

        // Updates scope when photo1 changes.
        $scope.$watch('photo1', function(nv, ov) {
          $scope.article.img1 = null;

          if ($scope.photo1) {
            $scope.article.img1 = $scope.photo1.id;

            if (angular.isUndefined($scope.article.img1_footer) ||
                (!angular.isUndefined(ov) && nv.id !== ov.id)) {
              $scope.article.img1_footer = $scope.photo1.description;
            }

            // Set inner image if empty
            if (angular.isUndefined($scope.photo2) && nv !== ov) {
              $scope.photo2 = $scope.photo1;
            }
          }
        }, true);

        // Updates scope when photo2 changes.
        $scope.$watch('photo2', function(nv, ov) {
          $scope.article.img2 = null;

          if ($scope.photo2) {
            $scope.article.img2 = $scope.photo2.id;

            if (angular.isUndefined($scope.article.img2_footer) ||
                (!angular.isUndefined(ov) && nv.id !== ov.id)) {
              $scope.article.img2_footer = $scope.photo2.description;
            }
          }
        }, true);

        //Updates scope when photo3 changes.
        $scope.$watch('photo3', function(nv, ov) {
          $scope.article.imageHome = null;

          if ($scope.photo3) {
            $scope.article.params.imageHome = $scope.photo3.id;

            if (angular.isUndefined($scope.article.params.imageHomeFooter) ||
                (!angular.isUndefined(ov) && nv.id !== ov.id)) {
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
              if ($scope.unsaved){
                return $window.leaveMessage;
              }
            });

            var key = 'article-draft';
            $scope.draftSaved = null;

            if (ov && nv !== ov && $scope.draftEnabled) {
              if ($scope.article.pk_article) {
                key = 'article-' + $scope.article.pk_article + '-draft';
              }

              webStorage.local.set(key, {
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

            $scope.unsaved = true;
          }, true);

        // Update title_int when title changes
        $scope.$watch('article.title', function(nv) {
          if (nv && (!$scope.article.title_int ||
              nv.substr(0, $scope.article.title_int.length) ===
              $scope.article.title_int)) {
            $scope.article.title_int = nv;
          }
        }, true);

        // Enable drafts after 5s to grant CKEditor initialization
        $timeout(function() {
          $scope.draftEnabled = true;
        }, 5000);

        $('#starttime, #endtime').datetimepicker({
          format: 'YYYY-MM-DD HH:mm:ss',
          useCurrent: false
        });

        $('#starttime').on('dp.change',function (e) {
          $('#endtime').data('DateTimePicker').minDate(e.date);

          $scope.$apply(function () {
            $scope.article.starttime =
              $window.moment(e.date).format('YYYY-MM-DD HH:mm:ss');
          });
        });

        $('#endtime').on('dp.change',function (e) {
          $('#starttime').data('DateTimePicker').maxDate(e.date);

          $scope.$apply(function() {
            $scope.article.endtime =
              $window.moment(e.date).format('YYYY-MM-DD HH:mm:ss');
          });
        });
      }
    ]);
})();
