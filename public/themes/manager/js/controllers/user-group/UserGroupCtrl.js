/**
 * Handles all actions in user groups listing.
 *
 * @param  Object $scope      The current scope.
 * @param  Object itemService The item service.
 * @param  Object data        The input data.
 *
 * @return Object The command controller.
 */
angular.module('ManagerApp.controllers').controller('UserGroupCtrl',
    function ($scope, itemService, data) {
        /**
         * List of available groups.
         *
         * @type Object
         */
        $scope.group = {
            privileges: []
        };

        /**
         * The template parameters.
         *
         * @type Object
         */
        $scope.template = data.template;

        // Initialize group
        if (data.group) {
            $scope.group = data.group;
        }
    }
);
