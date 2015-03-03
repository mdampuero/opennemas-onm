/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('WidgetCtrl', [
  '$controller', '$rootScope', '$scope', 'onmEditor',
  function($controller, $rootScope, $scope, onmEditor) {

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * Updates scope when photo1 changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('renderlet', function(nv, ov) {
      delete ov;
      if (nv === 'html') {
        // onmEditor.init;
      } else if (nv === 'smarty') {
          onmEditor.get('content').destroy();
          // Deactivate ck editor in widget_content
      }
    }, true);

    $scope.addParameter = function () {
      var source = $('#param-template').html();
      $('#params').append(source);
    }
  }
]);
