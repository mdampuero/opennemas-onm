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
    }
  }
]);
