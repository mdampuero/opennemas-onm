/**
 * Handles all actions in commands listing.
 *
 * @param  Object $scope      The current scope.
 * @param  Object itemService The item service.
 * @param  Object data        The input data.
 *
 * @return Object The command controller.
 */
angular.module('ManagerApp.controllers').controller('CommandCtrl',
    function ($scope, itemService, data) {
        /**
         * List of available commands.
         *
         * @type Object
         */
        $scope.name = data.name;

        $scope.output = data.output;
    }
);
