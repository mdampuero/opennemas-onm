/**
 * Controller to handle list actions.
 */
angular.module('BackendApp.controllers').controller('UserListCtrl', [
  '$http', '$modal', '$scope', '$timeout', 'itemService', 'routing', 'messenger', 'webStorage', 'oqlEncoder', 'queryManager', '$controller',
  function($http, $modal, $scope, $timeout, itemService, routing, messenger, webStorage, oqlEncoder, queryManager, $controller) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('ContentListCtrl', {$scope: $scope}));

    /**
     * Updates selected items.
     *
     * @param string route   Route name.
     * @param string name    Name of the property to update.
     * @param mixed  value   New value.
     * @param string loading Name of the property used to show work-in-progress.
     */
    $scope.updateSelectedItems = function(route, name, value, loading) {
      // Enable spinner
      $scope.deleting = 1;

      // Calculate backend access
      var backend_access = false;
      angular.forEach($scope.selected.contents, function(selected_value, selected_key) {
        angular.forEach($scope.contents, function(content_value, content_key) {
          if (selected_value == content_value.id && parseInt(content_value.type) == 0) {
            console.log(selected_value, content_value.id, content_value.type)
            backend_access = true;
          }
        });
      });

      var modal = $modal.open({
        templateUrl: 'modal-update-selected',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              checkPhone:  $scope.checkPhone,
              checkVat:    $scope.checkVat,
              extra:       $scope.extra,
              name:        name,
              saveBilling: $scope.saveBilling,
              selected:    $scope.selected,
              value:       value,
              backend_access: backend_access
            };
          },
          success: function() {
            return function() {
              // Load shared variable
              var selected = $scope.selected.contents;

              updateItemsStatus(loading, 1);

              var url = routing.generate(route,
                { contentType: $scope.criteria.content_type_name });

              return $http.post(url, { ids: selected, value: value });
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          $scope.renderMessages(response.data.messages);

          if (response.status === 200) {
            updateItemsStatus(loading, 0, name, value);
          }
        }

        $scope.selected.contents = [];
        $scope.selected.all = false;
      });
    };

}]);
