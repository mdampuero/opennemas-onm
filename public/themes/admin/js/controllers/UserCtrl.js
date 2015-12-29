/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('UserCtrl', [
  '$controller', '$http', '$modal', '$scope',
  function($controller, $http, $modal, $scope) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    $scope.confirmUser = function() {
      if ($scope.activated == '1') {
        var modal = $modal.open({
          templateUrl: 'modal-update-selected',
          backdrop: 'static',
          controller: 'modalCtrl',
          resolve: {
            template: function() {
              return {
                name:     'create',
                value:    1,
              };
            },
            success: function() {
              return null;
            }
          }
        });

        modal.result.then(function(response) {
          if (response) {
            $('form').submit();
          }
        });
      } else {
        $('form').submit();
      }
    };
  }
]);
