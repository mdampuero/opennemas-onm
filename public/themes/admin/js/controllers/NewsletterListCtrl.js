/**
 * Controller to handle list actions.
 */
angular.module('BackendApp.controllers').controller('NewsletterListCtrl', [
  '$http', '$uibModal', '$scope', '$timeout', 'itemService', 'routing', 'messenger', 'webStorage', 'oqlEncoder', 'queryManager', '$controller',
  function($http, $uibModal, $scope, $timeout, itemService, routing, messenger, webStorage, oqlEncoder, queryManager, $controller) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('ContentListCtrl', { $scope: $scope }));

    /**
     * Permanently removes a contents by using a confirmation dialog
     */
    $scope.removePermanently = function(content) {
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
              var url = routing.generate('backend_ws_newsletter_delete', { id: content.id });

              return $http.get(url);
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          $scope.renderMessages(response.data.messages);

          if (response.status === 200) {
            $scope.list($scope.route);
          }
        }
      });
    };
}]);
