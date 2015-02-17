/**
 * Controller to handle list actions.
 */
angular.module('BackendApp.controllers').controller('CacheManagerCtrl', [
  '$http', '$modal', '$scope', '$timeout', 'itemService', 'routing', 'messenger', 'webStorage', 'oqlEncoder', 'queryManager', '$controller',
  function($http, $modal, $scope, $timeout, itemService, routing, messenger, webStorage, oqlEncoder, queryManager, $controller) {

    // Initialize the super class and extend it.
    $.extend(this, $controller('ContentListController', {$scope: $scope}));

    /**
     * Permanently removes a contents by using a confirmation dialog
     */
    $scope.removePermanently = function(content) {

      console.log(content, content.cache_id, content.template);
      var modal = $modal.open({
        templateUrl: 'modal-cache-remove',
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
                'backend_ws_content_remove_permanently',
                { contentType: content.content_type_name, id: content.id }
              );

              return $http.post(url);
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          renderMessages(response.data.messages);

          if (response.status == 200) {
            $scope.list($scope.route);
          }
        }
      });
    };
    console.log(this);
}]);
