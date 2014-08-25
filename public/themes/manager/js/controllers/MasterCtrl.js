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

        /**
         * Cleans the criteria for the current listing.
         *
         * @param Object criteria The search criteria.
         *
         * @return Object The cleaned criteria.
         */
        $scope.cleanFilters = function (criteria) {
            var cleaned = {};

            for (var name in criteria) {
                for (var i = 0; i < criteria[name].length; i++) {
                    if (criteria[name][i]['value'] != -1
                        && criteria[name][i]['value'] !== ''
                    ){
                        if (criteria[name][i]['value']) {
                            var values = criteria[name][i]['value'].split(' ');

                            cleaned[name] = [];
                            for (var i = 0; i < values.length; i++) {
                                switch(criteria[name][i]['operator']) {
                                    case 'like':
                                        cleaned[name][i] = {
                                            value:    '%' + values[i] + '%',
                                            operator: 'LIKE'
                                        };
                                        break;
                                    case 'regexp':
                                        cleaned[name][i] = {
                                            value:    '(^' + values[i] + ',)|('
                                                + ',' + values[i] + ',)|('
                                                + values[i] + '$)',
                                            operator: 'REGEXP'
                                        };
                                        break;
                                    default:
                                        cleaned[name][i] = {
                                            value:    values[i],
                                            operator: criteria[name][i]['operator']
                                        };
                                }
                            };
                        } else {
                            if (!cleaned[name]) {
                                cleaned[name] = [];
                            }

                            cleaned[name][i] = {
                                value: criteria[name][i]['value']
                            };
                        }
                    }
                }
            };

            return cleaned;
        }
    }
);
