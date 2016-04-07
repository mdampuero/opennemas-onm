(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  CommandListCtrl
     *
     * @requires $scope
     * @requires itemService
     *
     * @description
     *   Displays the list of commands.
     */
    .controller('CommandListCtrl', [
        '$scope', 'itemService',
        function ($scope, itemService) {
          $scope.list = function() {
            itemService.list('manager_ws_commands_list', {}).then(function(response) {
              $scope.items = response.data.results;
              $scope.extra = response.data.extra;
            });
          };

          // Frees up memory before controller destroy event
          $scope.$on('$destroy', function() {
            $scope.items = null;
            $scope.extra = null;
          });

          $scope.list();
        }
    ]);
})();
