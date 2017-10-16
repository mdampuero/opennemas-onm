/**
 * Controller to handle list actions.
 */
angular.module('BackendApp.controllers').controller('TrashListCtrl', [
  '$http', '$uibModal', '$scope', '$timeout', 'itemService', 'routing', 'messenger', 'webStorage', 'Encoder', 'queryManager', '$controller',
  function($http, $uibModal, $scope, $timeout, itemService, routing, messenger, webStorage, Encoder, queryManager, $controller) {
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
}]);
