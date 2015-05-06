/**
 * Handles all actions in users listing.
 *
 * @param  Object $modal       The modal service.
 * @param  Object $scope       The current scope.
 * @param  Object itemService  The item service.
 * @param  Object routing The routing service.
 * @param  Object messenger    The messenger service.
 * @param  Object data         The input data.
 *
 * @return Object The user list controller.
 */
angular.module('ManagerApp.controllers').controller('ReportListCtrl', [
    '$modal', '$scope', 'itemService', 'routing', 'messenger', 'data',
    function ($modal, $scope, itemService, routing, messenger, data) {
        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {};

        /**
         * List of available users.
         *
         * @type {Object}
         */
        $scope.items = data.results;

        /**
         * List of filtered items.
         *
         * @type {Object}
         */
        $scope.filtered = $scope.items;

        /**
         * Marks variables to delete for garbage collector;
         */
        $scope.$on('$destroy', function() {
            $scope.criteria         = null;
            $scope.pagination.epp   = null;
            $scope.items            = null;
            $scope.selected         = null;
            $scope.orderBy          = null;
            $scope.pagination.page  = null;
            $scope.pagination.total = null;
        });
    }
]);
