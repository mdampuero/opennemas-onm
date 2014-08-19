/**
 * Handles all actions in user groups listing.
 *
 * @param  Object $scope      The current scope.
 * @param  Object itemService The item service.
 * @param  Object data        The input data.
 *
 * @return Object The command controller.
 */
angular.module('ManagerApp.controllers').controller('UserGroupListCtrl',
    function ($scope, itemService, data) {
        /**
         * List of available groups.
         *
         * @type Object
         */
        $scope.groups = data.results;
    }
);
