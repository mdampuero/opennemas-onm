/**
 * Controller to use in inner sections.
 */
angular.module('BackendApp.controllers').controller('InnerCtrl', [
  '$scope',
  function($scope) {
    // Initialize the scope with the input/select values.
    $('input, select').each(function() {
      var name = $(this).attr('name');
      $scope[name] = $(this).val();
    });
  }
]);
