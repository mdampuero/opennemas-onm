(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  CommandCtrl
     *
     * @requires $routeParams
     * @requires $scope
     * @requires itemService
     *
     * @description
     *   Executes a command.
     */
    .controller('CommandCtrl', [
      '$routeParams', '$scope', 'itemService',
      function ($routeParams, $scope, itemService) {
        /**
         * Frees up memory before controller destroy event
         */
        $scope.$on('$destroy', function() {
          $scope.name   = null;
          $scope.output = null;
        });

        itemService.executeCommand('manager_ws_command_output',
            $routeParams.command, $routeParams.data).then(function(response) {
          $scope.name   = response.data.name;
          $scope.output = response.data.output;
        });
      }
    ]);
})();
