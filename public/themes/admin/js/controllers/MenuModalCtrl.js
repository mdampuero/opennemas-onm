
angular.module('BackendApp.controllers').controller('MenuModalCtrl', [
  '$uibModalInstance', '$scope',
  function ($uibModalInstance, $scope) {
    'use strict';

    $scope.selected = [];

    $scope.type = 'external';

    /**
     * Confirms and executes the confirmed action.
     */
    $scope.addItem = function() {
      if ($scope.type === 'external') {
        var items = [];

        if($scope.externalLinkUrl && $scope.externalLinkUrl !== '') {
          items.push({
            link:    $scope.externalLinkUrl,
            pk_item: $scope.externalLinkTitle,
            title:   $scope.externalLinkTitle,
            type:    $scope.type,
            submenu: []
          });
        }

        $uibModalInstance.close({ items: items });
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

        if (
          $scope.type === 'syncCategory' ||
          $scope.type === 'syncBlogCategory'
        ) {
          item.link  = $scope.selected[i];
          item.title = $scope.selected[i];
        }

        items.push(item);
      }

      $uibModalInstance.close({ items: items });
    };

    /**
     * Closes the current modal
     */
     $scope.close = function() {
      $uibModalInstance.dismiss();
    };

    /**
     * Resets the list of selected items when item type changes.
     */
    $scope.$watch('type', function() {
      $scope.selected = [];
    });
  }
]);
