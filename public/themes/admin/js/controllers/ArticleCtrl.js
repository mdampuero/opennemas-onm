/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('ArticleCtrl', [
  '$controller', '$rootScope', '$scope', 'onmEditor', 'renderer',
  function($controller, $rootScope, $scope, onmEditor, renderer) {

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    $rootScope.$on('MediaPicker.insert', function (event, args) {
      if (/editor.*/.test(args.target)) {
        var target = args.target.replace('editor.', '');
        return $scope.insertInCKEditor(target, args.items);
      }

      $scope.insertInModel(args.target, args.items);
    });

    /**
     * Removes the given image from the scope.
     *
     * @param string image The image to remove.
     */
    $scope.removeImage = function(image) {
      delete $scope[image];
    };

    /**
     * Inserts an array of items in a CKEditor instance.
     *
     * @param string target The target id.
     * @param array  items  The items to insert.
     */
    $scope.insertInCKEditor = function(target, items) {
      var html;

      if (items instanceof Array) {
        for (var i = 0; i < items.length; i++) {
          html = renderer.renderImage(items[i]);
          onmEditor.get(target).insertHtml(html);
        }

        return;
      }

      html = renderer.renderImage(items);
      onmEditor.get(target).insertHtml(html);
    };

    /**
     * Updates the scope with the items.
     *
     * @param string target The property to update.
     * @param array  items  The new property value.
     */
    $scope.insertInModel = function(target, items) {
      $scope[target] = items;
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
     * Removes an item from an array of related items.
     *
     * @param string  from  The array name in the current scope.
     * @param integer index The index of the element to remove.
     */
    $scope.removeItem = function(from, index) {
      $scope[from].splice(index, 1);
    };

    /**
     * Updates scope when photo1 changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('photo1', function(nv, ov) {
      $scope.img1        = null;
      $scope.img1_footer = null;

      if ($scope.photo1) {
        $scope.img1        = $scope.photo1.id;
        $scope.img1_footer = $scope.photo1.description;
      }
    }, true);

    /**
     * Updates scope when photo2 changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('photo2', function(nv, ov) {
      $scope.img2        = null;
      $scope.img2_footer = null;

      if ($scope.photo2) {
        $scope.img2        = $scope.photo2.id;
        $scope.img2_footer = $scope.photo2.description;
      }
    }, true);

    /**
     * Updates scope when photo3 changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('photo3', function(nv, ov) {
      $scope.imageHome       = null;
      $scope.imageHomeFooter = null;

      if ($scope.photo3) {
        $scope.imageHome       = $scope.photo3.id;
        $scope.imageHomeFooter = $scope.photo3.description;
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
      var items           = [];
      $scope.relatedFront = [];

      if (nv instanceof Array) {
        for (var i = 0; i < nv.length; i++) {
          items.push({ id: nv[i].id, position: i });
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
          items.push({ id: nv[i].id, position: i });
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
          items.push({ id: nv[i].id, position: i });
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
  }
]);
