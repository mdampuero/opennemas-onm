/**
 * Handles all actions in commands listing.
 *
 * @param  Object $scope      The current scope.
 * @param  Object itemService The item service.
 * @param  Object data        The input data.
 *
 * @return Object The command controller.
 */
angular.module('ManagerApp.controllers').controller('CommandCtrl', [
    '$scope', 'itemService', 'data',
    function ($scope, itemService, data) {
        /**
         * List of available commands.
         *
         * @type Object
         */
        $scope.name = data.name;

        /**
         * Output of the command
         */
        $scope.output = data.output;

        /**
         * Frees up memory before controller destroy event
         */
        $scope.$on('$destroy', function() {
            $scope.name  = null;
            $scope.output   = null;
        });
    }
]);
