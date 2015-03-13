
angular.module('BackendApp.controllers').controller('MenuModalCtrl', [
  '$modalInstance', '$scope',
  function ($modalInstance, $scope) {
    'use strict';

    $scope.selected = [];

    $scope.type = 'external';

    /**
     * Confirms and executes the confirmed action.
     */
    $scope.addItem = function() {
      if ($scope.type === 'external') {
        var item = {
          link:    $scope.externalLinkUrl,
          pk_item: $scope.externalLinkTitle,
          title:   $scope.externalLinkTitle,
          type:    $scope.type,
          submenu: []
        };

        $modalInstance.close({ items: [ item ] });

        return;
      }

      var items = [];
      for (var i = 0; i < $scope.selected.length; i++) {
        var item = {
          link:    $scope.selected[i].name,
          pk_item: $scope.selected[i].id,
          title:   $scope.selected[i].title,
          type:    $scope.type,
          submenu: []
        };

        if ($scope.type === 'static') {
          item.link = $scope.selected[i].slug;
        }

        if ($scope.type === 'internal') {
          item.link = $scope.selected[i].link;
        }

        items.push(item);
      }

      $modalInstance.close({ items: items });
    };

    /**
     * Closes the current modal
     */
     $scope.close = function() {
      $modalInstance.dismiss();
    };

    /**
     * Resets the list of selected items when item type changes.
     */
    $scope.$watch('type', function() {
      $scope.selected = [];
    });
  }
]);
