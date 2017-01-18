/**
 * Controller to handle list actions.
 */
angular.module('BackendApp.controllers').controller('NewsletterSubscriptorListCtrl', [
  '$http', '$uibModal', '$scope', '$timeout', 'itemService', 'routing', 'messenger', 'webStorage', 'oqlEncoder', 'queryManager', '$controller',
  function($http, $uibModal, $scope, $timeout, itemService, routing, messenger, webStorage, oqlEncoder, queryManager, $controller) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('ContentListCtrl', {$scope: $scope}));

    /**
     * Permanently removes a contents by using a confirmation dialog
     */
    $scope.delete = function(content) {
      var modal = $uibModal.open({
        templateUrl: 'modal-delete',
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
              var url = routing.generate('backend_ws_newsletter_subscriptor_delete', { id: content.id });

              return $http.get(url);
            };
          }
        }
      });

      modal.result.then(function(response) {
        messenger.post(response.data.messages);

        if (response.success) {
          $scope.list($scope.route);
        }
      });
    };

    /**
     * Permanently removes a list of contents by using a confirmation dialog
     */
    $scope.deleteSelected = function () {
      // Enable spinner
      $scope.deleting = 1;

      var modal = $uibModal.open({
        templateUrl: 'modal-delete-selected',
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
              var url = routing.generate('backend_ws_newsletter_subscriptor_batch_delete');

              return $http.post(url, { selected: $scope.selected.contents});
            };
          }
        }
      });

      modal.result.then(function(response) {
        messenger.post(response.data.messages);

        $scope.selected.total = 0;
        $scope.selected.contents = [];

        if (response.success) {
          $scope.list($scope.route);
        }
      });
    };
}]);
