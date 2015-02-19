/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('ArticleCtrl', [
  '$controller', '$rootScope', '$scope', 'onmEditor', 'renderer',
  function($controller, $rootScope, $scope, onmEditor, renderer) {

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    $rootScope.$on('MediaPicker.insert', function (event, args) {
      for (var i = 0; i < args.items.length; i++) {
        var html = renderer.renderImage(args.items[i]);
        console.log(html);
        onmEditor.get(args.target).insertHtml(html);
      };
    });
  }
]);
