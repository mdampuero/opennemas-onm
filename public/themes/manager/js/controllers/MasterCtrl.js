/**
 * Controller to implement common actions.
 *
 * @param Object $location    The location service.
 * @param Object $scope       The current scope.
 * @param Object fosJsRouting The fosJsRouting service.
 */
angular.module('ManagerApp.controllers').controller('MasterCtrl', [
    '$http', '$location', '$modal', '$rootScope', '$scope', '$window', 'vcRecaptchaService',
    'httpInterceptor', 'authService', 'fosJsRouting',
    function (
        $http, $location, $modal, $rootScope, $scope, $window, vcRecaptchaService,
        httpInterceptor, authService, fosJsRouting
    ) {
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
         * Flag to show modal window for login only once.
         *
         * @type boolean
         */
        $scope.auth = {
            status: true,
            modal: false,
            inprogress: false
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

        $scope.init = function() {
            $('body').removeClass('application-loading');

            $scope.isAuthenticated();
        }

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
         * Checks and loads an user if it is authenticated in the system.
         */
        $scope.isAuthenticated = function() {
            authService.isAuthenticated('manager_ws_auth_check_user')
                .then(function (response) {
                    if (response.data.success) {
                        $scope.user        = response.data.user;
                        $scope.auth.status = true;
                        $scope.auth.modal  = false;
                    }
                });
        }

        /**
         * Logs an user in.
         */
        $scope.login = function() {
            $scope.loading = 1;

            var data = {
                _username: $scope.username,
                _password: $scope.password,
                _token:    $scope.token,
            }

            authService.login('manager_ws_auth_check', data, $scope.attempts)
                .then(function (response) {
                    if (response.data.success) {
                        $scope.auth.status     = true;
                        $scope.auth.inprogress = false;
                        $scope.auth.modal      = true;
                        $scope.user            = response.data.user;

                        httpInterceptor.loginConfirmed();
                    } else {
                        $scope.token    = response.data.token;
                        $scope.attempts = response.data.attempts;
                        $scope.message  = response.data.message;
                    }

                    $scope.loading = 0;
                }
            );
        }

        /**
         * Logs the user out.
         */
        $scope.logout = function() {
            var modal = $modal.open({
                templateUrl: 'modal-confirm',
                controller:  'modalCtrl',
                resolve: {
                    template: function() {
                        return {
                            name: 'logout'
                        };
                    },
                    success: function() {
                        return function() {
                            return authService.logout('manager_ws_auth_logout');
                        }
                    }
                }
            });

            modal.result.then(function (response) {
                if (response) {
                    $scope.auth = {
                        status:     false,
                        modal:      false,
                        inprogress: true
                    };

                    $scope.token    = response.data.token;
                    $scope.attempts = response.data.attempts;
                }
            });
        }

        /**
         * Force page reload.
         */
        $scope.reload = function() {
            $scope.loading = 1;

            $window.location.reload();
        }

        /**
         * Shows the login form when login is required.
         *
         * @param Object event The event object.
         * @param array  args  The list of arguments.
         */
        $scope.$on('auth-login-required', function (event, args) {
            $scope.auth.status = false;

            if (!$scope.auth.inprogress) {
                $scope.auth.inprogress = true;

                if ($scope.auth.modal) {
                    var modal = $modal.open({
                        templateUrl: 'modal-login',
                        backdrop: 'static',
                        controller: 'LoginModalCtrl',
                        resolve: {
                            data: function() {
                                var url = fosJsRouting.generate('manager_ws_auth_login');

                                return $http.post(url).then(function (response) {
                                    return response.data;
                                });
                            }
                        }
                    });

                    modal.result.then(function (data) {
                        $scope.auth.status = data.success;

                        if (data.success) {
                            $scope.auth.inprogress = false;
                            $scope.user            = data.user;

                            httpInterceptor.loginConfirmed();
                        }
                    });
                } else {
                    var url = fosJsRouting.generate('manager_ws_auth_login');

                    $http.post(url).then(function (response) {
                        $scope.token     = response.data.token;
                        $scope.attempts  = response.data.attempts;
                    });
                }
            }
        });

        function refreshApp() {
            var host = document.getElementById('view');
            if(host) {
                var mainDiv = $("#view");
                mainDiv.empty();
                angular.element(host).empty();
            }
        }

        $rootScope.$on('$routeChangeStart',
            function (event, next, current) {
                refreshApp();
            }
        );

        /**
         * Shows a modal to force page reload.
         *
         * @param Object event The event object.
         * @param array  args  The list of arguments.
         */
        $scope.$on('application-need-upgrade', function (event, args) {
            var modal = $modal.open({
                templateUrl: 'modal-upgrade',
                controller: 'MasterCtrl',
                backdrop: 'static'
            });
        });
    }
]);
