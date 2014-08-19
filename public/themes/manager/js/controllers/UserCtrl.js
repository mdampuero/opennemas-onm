/**
 * Handles all actions in users listing.
 *
 * @param  Object $scope      The current scope.
 * @param  Object itemService The item service.
 * @param  Object data        The input data.
 *
 * @return Object The command controller.
 */
angular.module('ManagerApp.controllers').controller('UserCtrl',
    function ($scope, itemService, data) {
        /**
         * List of available users.
         *
         * @type Object
         */
        $scope.user = data.data;
    }
);
