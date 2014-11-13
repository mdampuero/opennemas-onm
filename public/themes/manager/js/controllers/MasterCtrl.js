/**
 * Controller to implement common actions.
 *
 * @param Object $location    The location service.
 * @param Object $scope       The current scope.
 * @param Object fosJsRouting The fosJsRouting service.
 */
angular.module('ManagerApp.controllers').controller('MasterCtrl', [
    '$filter', '$http', '$location', '$modal', '$rootScope', '$scope',
    '$translate', '$timeout', '$window', 'vcRecaptchaService', 'httpInterceptor',
    'authService', 'fosJsRouting', 'history', 'messenger', 'paginationConfig',
    function (
        $filter, $http, $location, $modal, $rootScope, $scope, $translate, $timeout,
        $window, vcRecaptchaService, httpInterceptor, authService, fosJsRouting,
        history, messenger, paginationConfig
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
        $scope.sidebar = {
            wanted: 0,
            current: 0,
            forced: 0
        };

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
         * Clears the last value for the current path.
         */
        $scope.clear = function(url) {
            history.clear(url);
        }

        /**
         * Removes a class from body and checks if user is authenticated.
         */
        $scope.init = function(language) {
            $('body').removeClass('application-loading');

            $translate.use(language);

            paginationConfig.nextText     = $filter('translate')('Next');
            paginationConfig.previousText = $filter('translate')('Previous');

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

                        fakeLogin();
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

        /**
         * Submits a fake login form and force browsers to save credentials.
         */
        function fakeLogin() {
            var iframe    = document.getElementById("fake-login");
            var iframedoc = iframe.contentWindow ? iframe.contentWindow.document : iframe.contentDocument;

            var fakeForm     = iframedoc.getElementById("fake-login-form");
            var fakeUsername = iframedoc.getElementById("username");
            var fakePassword = iframedoc.getElementById("password");

            fakeUsername.value = $scope.username;
            fakePassword.value = $scope.password;
            fakeForm.submit();
        }

        /**
         * Empties ng-view when route changes.
         *
         * @param Object event The event object.
         * @param array  args  The list of arguments.
         */
        $rootScope.$on('$routeChangeStart', function (event, next, current) {
            history.restore($location.path());
            history.push($location.path(), $location.search());
        });

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

        /**
         * Shows a message when an error while sending an Ajax request occurs.
         *
         * @param Object event The event object.
         * @param array  args  The list of arguments.
         */
        $scope.$on('http-error', function (event, args) {
            messenger.post({
                type: 'error',
                message: args.data.text
            });
        });

        /**
         * Updates sidebar status when window width changes.
         *
         * @param integer nv New width value.
         * @param integer ov Old width value.
         */
        $scope.$watch('windowWidth', function(nv, ov) {
            if (nv <= 1024)  {
                $scope.sidebar.forced  = 1;
                $scope.sidebar.current = 1;
            } else {
                $scope.sidebar.forced = 0;
                $scope.sidebar.current = $scope.sidebar.wanted;
            }

            $scope.checkFiltersBar();
        });

        /**
         * Updates the content margin-top basing on the filters-navbar height.
         */
        $scope.checkFiltersBar = function checkFiltersBar() {
            $timeout(function() {
                if ($('.filters-navbar').length != 1) {
                    return false;
                }

                var margin = 50 + $('.filters-navbar').height() - 15;

                $('.content').css('margin-top', margin + 'px');
            }, 1000);
        }
    }
]);
