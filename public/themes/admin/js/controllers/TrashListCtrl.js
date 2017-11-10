/**
 * Controller to handle list actions.
 */
angular.module('BackendApp.controllers').controller('TrashListCtrl', [
  '$controller', '$http', '$uibModal', '$scope', 'localizer', 'messenger', 'routing',
  function($controller, $http, $uibModal, $scope, localizer, messenger, routing) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('ContentListCtrl', {$scope: $scope}));

    /**
     * Permanently removes a contents by using a confirmation dialog
     */
    $scope.removeAll = function(content) {
      var modal = $uibModal.open({
        templateUrl: 'modal-remove-all',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              content: content
            };
          },
          success: function() {
            return function() {
              var url = routing.generate('backend_ws_contents_empty_trash');

              return $http.get(url);
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          messenger.post(response.data.messages);

          if (response.success) {
              $scope.list($scope.route);
          }
        }
      });
    };

    /**
     * Takes out of trash a content by using a confirmation dialog
     */
    $scope.restoreFromTrash = function(content) {
      var modal = $uibModal.open({
        templateUrl: 'modal-restore-from-trash',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              content: content
            };
          },
          success: function() {
            return function() {
              var url = routing.generate(
                'backend_ws_content_restore_from_trash',
                { contentType: content.content_type_name, id: content.id }
              );

              return $http.post(url);
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          messenger.post(response.data.messages);

          if (response.success) {
            $scope.list($scope.route);
          }
        }
      });
    };

    /**
     * Takes out of trash a list of contents by using a confirmation dialog
     */
    $scope.restoreFromTrashSelected = function () {
      // Enable spinner
      $scope.deleting = 1;

      var modal = $uibModal.open({
        templateUrl: 'modal-batch-restore',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              selected: $scope.selected
            };
          },
          success: function() {
            return function() {
              var url = routing.generate(
                'backend_ws_contents_batch_restore_from_trash',
                { contentType: 'content' }
              );

              return $http.post(url, {ids: $scope.selected.contents});
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          messenger.post(response.data.messages);

          $scope.selected.total = 0;
          $scope.selected.contents = [];

          if (response.success) {
            $scope.list($scope.route);
          }
        }
      });
    };

    // Localize titles when content list changes
    $scope.$watch('contents', function(nv, ov) {
      if (nv === ov) {
        return;
      }

      var lz   = localizer.get($scope.extra.options);
      var keys = [ 'title' ];

      $scope.contents = lz.localize(nv, keys,
        $scope.extra.options.default);
    }, true);
}]);
