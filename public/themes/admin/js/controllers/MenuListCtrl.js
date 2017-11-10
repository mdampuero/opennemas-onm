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
   *
   * @description
   *   Controller for opinion list.
   */
  .controller('MenuListCtrl', [
    '$controller', '$location', '$uibModal', '$scope', 'http', 'routing', 'messenger', 'oqlEncoder',
    function($controller, $location, $uibModal, $scope, http, routing, messenger, oqlEncoder) {
      // Initialize the super class and extend it.
      $.extend(this, $controller('ContentListCtrl', { $scope: $scope }));

      /**
       * Updates the array of contents.
       *
       * @param string route Route name.
       */
      $scope.list = function(route) {
        $scope.contents = [];
        $scope.loading  = 1;
        $scope.selected = { all: false, contents: [] };

        oqlEncoder.configure({
          placeholder: {
            name: 'name ~ "%[value]%"',
          }
        });

        var oql   = oqlEncoder.getOql($scope.criteria);
        var route = {
          name: $scope.route,
          params:  { oql: oql }
        };

        $location.search('oql', oql);

        http.get(route).then(function(response) {
          $scope.total    = parseInt(response.data.total);
          $scope.contents = response.data.results;

          $scope.getContentsLocalizeTitle();

          if (response.data.hasOwnProperty('extra')) {
            $scope.extra = response.data.extra;
          }

          // Disable spinner
          $scope.loading = 0;
        }, function () {
          $scope.loading = 0;
          var params = {
            id: new Date().getTime(),
            message: 'Error while fetching data from backend',
            type: 'error'
          };

          messenger.post(params);
        });
      };

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
                return http.post('backend_ws_menus_batch_delete',
                  {ids: $scope.selected.contents});
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
                var route = {
                  name: 'backend_ws_menu_delete',
                  params: { id: content.id }
                };

                return http.post(route);
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
