/**
 * Controller to implement common actions.
 *
 * @param Object $location    The location service.
 * @param Object $scope       The current scope.
 * @param Object fosJsRouting The fosJsRouting service.
 */
angular.module('ManagerApp.controllers').controller('MasterCtrl',
    function ($location, $scope, fosJsRouting) {

        $scope.isActive = function(route) {
            var url = fosJsRouting.generate(route);
            return $location.path() == url;
        }
    }
);
