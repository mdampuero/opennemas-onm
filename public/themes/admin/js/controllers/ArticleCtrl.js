/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('ArticleCtrl', [
  '$controller', '$uibModal', '$rootScope', '$scope', '$timeout', '$window', 'Editor', 'http', 'messenger', 'routing', 'webStorage',
  function($controller, $uibModal, $rootScope, $scope, $timeout, $window, Editor, http, messenger, routing, webStorage) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

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
              $scope.article = webStorage.get(key);
              modalWindow.close({ response: true, success: true });

              // Force Editor update
              Editor.get('summary').setData($scope.article.summary);
              Editor.get('body').setData($scope.article.body);

              // Force metadata
              for (var tag of $scope.article.metadata.split(',')) {
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
     * Opens a modal with the preview of the article.
     *
     * @param {String} previewUrl    The URL to generate the preview.
     * @param {String} getPreviewUrl The URL to get the preview.
     */
    $scope.preview = function(previewUrl, getPreviewUrl) {
      $scope.loading = true;

      // Force Editor update
      Editor.get('body').updateElement();
      Editor.get('summary').updateElement();

      var data = {'contents': $('#formulario').serializeArray()};
      var url  = routing.generate(previewUrl);

      http.post(url, data).success(function() {
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
     * Removes an album.
     *
     * @param string from The album name in the current scope.
     */
    $scope.removeAlbum = function(from) {
      delete $scope[from];
    };

    /**
     * Updates scope when photo1 changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('photo1', function(nv, ov) {
      $scope.img1 = null;

      if ($scope.photo1) {
        $scope.img1 = $scope.photo1.id;

        if (angular.isUndefined($scope.img1_footer)
          || angular.isUndefined(ov)
          || nv.id !== ov.id
        ) {
          $scope.img1_footer = $scope.photo1.description;
        }

        // Set inner image if empty
        if (angular.isUndefined($scope.photo2) && nv != ov) {
          $scope.photo2 = $scope.photo1;
        }
      }
    }, true);

    /**
     * Updates scope when photo2 changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('photo2', function(nv, ov) {
      $scope.img2 = null;

      if ($scope.photo2) {
        $scope.img2 = $scope.photo2.id;

        if (angular.isUndefined($scope.img2_footer)
          || angular.isUndefined(ov)
          || nv.id !== ov.id
        ) {
          $scope.img2_footer = $scope.photo2.description;
        }
      }
    }, true);

    /**
     * Updates scope when photo3 changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('photo3', function(nv, ov) {
      $scope.imageHome = null;

      if ($scope.photo3) {
        $scope.imageHome = $scope.photo3.id;

        if (angular.isUndefined($scope.imageHomeFooter)
          || angular.isUndefined(ov)
          || nv.id !== ov.id
        ) {
          $scope.imageHomeFooter = $scope.photo3.description;
        }
      }
    }, true);

    /**
     * Updates scope when video1 changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('video1', function(nv, ov) {
      $scope.fk_video     = null;
      $scope.footer_video = null;

      if ($scope.video1) {
        $scope.fk_video     = $scope.video1.id;
        $scope.footer_video = $scope.video1.description;

        // Set inner video if empty
        if (angular.isUndefined($scope.video2) && nv != ov) {
          $scope.video2 = $scope.video1;
        }
      }
    }, true);

    /**
     * Updates scope when video2 changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('video2', function(nv, ov) {
      $scope.fk_video2     = null;
      $scope.footer_video2 = null;

      if ($scope.video2) {
        $scope.fk_video2     = $scope.video2.id;
        $scope.footer_video2 = $scope.video2.description;
      }
    }, true);

    /**
     * Updates scope when relatedInFrontpage changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('relatedInFrontpage', function(nv, ov) {
      // Set inner related if empty or equal to front
      if ((!$scope.relatedInInner||
        $scope.relatedInner == $scope.relatedFront) && nv != ov
      ) {
        $scope.relatedInInner = angular.copy(nv);
      }

      var items           = [];
      $scope.relatedFront = [];

      if (nv instanceof Array) {
        for (var i = 0; i < nv.length; i++) {
          items.push({ id: nv[i].id, position: i, content_type: nv[i].content_type_name });
        }
      }

      $scope.relatedFront = angular.toJson(items);
    }, true);

    /**
     * Updates scope when relatedInInner changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('relatedInInner', function(nv, ov) {
      var items           = [];
      $scope.relatedInner = [];

      if (nv instanceof Array) {
        for (var i = 0; i < nv.length; i++) {
          items.push({ id: nv[i].id, position: i, content_type: nv[i].content_type_name });
        }
      }

      $scope.relatedInner = angular.toJson(items);
    }, true);

    /**
     * Updates scope when relatedInHome changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('relatedInHome', function(nv, ov) {
      var items          = [];
      $scope.relatedHome = [];

      if (nv instanceof Array) {
        for (var i = 0; i < nv.length; i++) {
          items.push({ id: nv[i].id, position: i, content_type: nv[i].content_type_name });
        }
      }

      $scope.relatedHome = angular.toJson(items);
    }, true);


    /**
     * Updates the model when galleryForFrontpage changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('galleryForFrontpage', function(nv, ov) {
      delete $scope.withGallery;

      if (nv) {
        $scope.withGallery = nv.id;
      }
    }, true);

    /**
     * Updates the model when galleryForInner changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('galleryForInner', function(nv, ov) {
      delete $scope.withGalleryInt;

      if (nv) {
        $scope.withGalleryInt = nv.id;
      }
    }, true);

    /**
     * Updates the model when galleryForHome changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('galleryForHome', function(nv, ov) {
      delete $scope.withGalleryHome;

      if (nv) {
        $scope.withGalleryHome = nv.id;
      }
    }, true);

    $scope.$watch('article', function(nv, ov) {
      var key = 'article-draft';

      if (ov && nv !== ov && $scope.draftEnabled) {
        if (nv.pk_article) {
          key = 'article-' + nv.pk_article + '-draft';
        }

        webStorage.local.set(key, nv);
      }
    }, true);

    $('form').submit(function() {
      var key = 'article-draft';

      if ($scope.article.pk_article) {
        key = 'article-' + $scope.article.pk_article + '-draft';
      }

      webStorage.local.remove(key);
    });

    // Enabled drafts after 10s
    $timeout(function() {
      $scope.draftEnabled = true;
    }, 10000);
  }
]);
