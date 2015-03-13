/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('MenuCtrl', [
  '$controller', '$http', '$modal', '$rootScope', '$scope', 'routing',
  function($controller, $http, $modal, $rootScope, $scope, routing) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * Deletes an item from the menu.
     *
     * @param {integer} index The index of the item to remove.
     */
    $scope.removeItem = function(index) {
      console.log(index);
      $scope.menu.items.splice(index, 1);
    };

    /**
     * Opens a modal window to add item to menu.
     */
    $scope.open = function() {
      var modal = $modal.open({
        templateUrl: 'modal-add-item',
        backdrop: 'static',
        controller: 'MenuModalCtrl'
      });

      modal.result.then(function(response) {
        $scope.menu.items = $scope.menu.items.concat(response.items);
      });
    };
  }
]);
