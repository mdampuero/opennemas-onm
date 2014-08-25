/**
 * Handles all actions in commands listing.
 *
 * @param  Object $scope      The current scope.
 * @param  Object itemService The item service.
 * @param  Object data        The input data.
 *
 * @return Object The command controller.
 */
angular.module('ManagerApp.controllers').controller('OpcacheCtrl',
    function ($scope, itemService, data) {
        /**
         * Opcache statistics
         *
         * @type Object
         */
        $scope.serverData = data;

        console.log($scope.serverData, !!!$scope.serverData.not_suported_messsage)

    }
);
