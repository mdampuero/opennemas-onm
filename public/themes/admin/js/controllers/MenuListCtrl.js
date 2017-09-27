angular.module('BackendApp.controllers')
  /**
   * @ngdoc controller
   * @name  OpinionListCtrl
   *
   * @requires $controller
   * @requires $http
   * @requires $scope
   * @requires routing
   * @requires messenger
   * @requires oqlEncoder
   * @requires queryManager
   *
   * @description
   *   Controller for opinion list.
   */
  .controller('MenuListCtrl', [
    '$controller', '$http', '$scope', 'routing', 'messenger', 'Encoder', 'queryManager',
    function($controller, $http, $scope, routing, messenger, Encoder, queryManager) {

      // Initialize the super class and extend it.
      $.extend(this, $controller('ContentListCtrl', { $scope: $scope }));

      /**
       * Permanently removes a list of contents by using a confirmation dialog
       */
      $scope.removeSelectedMenus = function () {
        // Enable spinner
        $scope.deleting = 1;

        var modal = $uibModal.open({
          templateUrl: 'modal-batch-remove-permanently',
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
                var url = routing.generate('backend_ws_menus_batch_delete');

                return $http.post(url, {ids: $scope.selected.contents});
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

      /**
       * Permanently removes a menu by using a confirmation dialog
       */
      $scope.removeMenu = function(content) {
        var modal = $uibModal.open({
          templateUrl: 'modal-remove-permanently',
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
                  'backend_ws_menu_delete',
                  { id: content.id }
                );

                return $http.post(url);
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

  }]);
