'use strict';

/**
 * Controller to use in inner sections.
 */
angular.module('BackendApp.controllers').controller('FrontpageCtrl', [
  '$controller', '$http', '$modal', '$scope',
  function($controller, $http, $modal, $scope) {
    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    $scope.preview = function(category) {
      $modal.open({
        templateUrl: 'modal-preview',
        controller: 'FrontpageModalCtrl',
        resolve: {
          template: function() {
            return {
              category: category
            };
          },
          success: function() {
            return null;
          }
        }
      });
    };
  }
]);
