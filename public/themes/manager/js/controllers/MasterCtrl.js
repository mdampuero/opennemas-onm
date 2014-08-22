/**
 * Controller to implement common actions.
 *
 * @param Object $location    The location service.
 * @param Object $scope       The current scope.
 * @param Object fosJsRouting The fosJsRouting service.
 */
angular.module('ManagerApp.controllers').controller('MasterCtrl',
    function ($location, $scope, fosJsRouting) {
        /**
         * The fosJsRouting service.
         *
         * @type Object
         */
        $scope.fosJsRouting = fosJsRouting;

        /**
         * The sidebar toggle status.
         *
         * @type integer
         */
        $scope.mini = 0;

        /**
         * Checks if the section is active.
         *
         * @param  string route Route name of the section to check.
         *
         * @return True if the current section
         */
        $scope.isActive = function(route) {
            var url = fosJsRouting.ngGenerateShort('/manager', route);
            return $location.path() == url;
        }

        /**
         * Toggles the sidebar.
         *
         * @param integer status The toggle status.
         */
        $scope.toggle = function(status) {
            $scope.mini = status;
        }

        /**
         * Closes the sidebar on click in small devices.
         */
        $scope.go = function() {
            if (angular.element('body').hasClass('breakpoint-480')) {
                $.sidr('close', 'main-menu');
                $.sidr('close', 'sidr');
            }
        }
    }
);
