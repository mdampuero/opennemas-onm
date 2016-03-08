'use strict';

/**
 * Handles all actions in commands listing.
 *
 * @param  Object $scope      The current scope.
 * @param  Object itemService The item service.
 * @param  Object data        The input data.
 *
 * @return Object The command controller.
 */
angular.module('ManagerApp.controllers').controller('CommandListCtrl', [
    '$scope', 'data',
    function ($scope, data) {
        /**
         * List of available commands.
         *
         * @type Object
         */
        $scope.commands = data.results;

        /**
         * List of instances (to clear smarty-cache)
         *
         * @type Object
         */
        $scope.template = data.template;

        /**
         * Frees up memory before controller destroy event
         */
        $scope.$on('$destroy', function() {
            $scope.commands = null;
            $scope.template = null;
        })
    }
]);
