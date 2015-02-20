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
    $scope.clear = function(image) {
      delete $scope[image];
    };

    /**
     * Inserts an array of items in a CKEditor instance.
     *
     * @param string target The target id.
     * @param array  items  The items to insert.
     */
    $scope.insertInCKEditor = function(target, items) {
      if (items instanceof Array) {
        for (var i = 0; i < items.length; i++) {
          var html = renderer.renderImage(items[i]);
          onmEditor.get(target).insertHtml(html);
        };

        return;
      }

      var html = renderer.renderImage(items);
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
     * Updates scope when photo1, photo2 or photo3 change.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('[photo1,photo2,photo3]', function(nv, ov) {
      $scope.img1        = null;
      $scope.img1_footer = null;

      if ($scope.photo1) {
        $scope.img1        = $scope.photo1.id;
        $scope.img1_footer = $scope.photo1.description;
      }

      $scope.img2        = null;
      $scope.img2_footer = null;

      if ($scope.photo2) {
        $scope.img2        = $scope.photo2.id;
        $scope.img2_footer = $scope.photo2.description;
      }

      $scope['params[imageHome]']       = null;
      $scope['params[imageHomeFooter]'] = null;

      if ($scope.photo3) {
        $scope['params[imageHome]']       = $scope.photo3.id;
        $scope['params[imageHomeFooter]'] = $scope.photo3.description;
      }
    }, true);
  }
]);
