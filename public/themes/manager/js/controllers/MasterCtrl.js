/**
 * Controller to implement common actions.
 *
 * @param Object $location    The location service.
 * @param Object $scope       The current scope.
 * @param Object routing The routing service.
 */
angular.module('ManagerApp.controllers').controller('MasterCtrl', [
    '$filter', '$http', '$location', '$modal', '$rootScope', '$scope',
    '$translate', '$timeout', '$window', 'vcRecaptchaService', 'httpInterceptor',
    'authService', 'routing', 'history', 'webStorage', 'messenger',
    'paginationConfig', 'cfpLoadingBar', 'sidebar',
    function (
        $filter, $http, $location, $modal, $rootScope, $scope, $translate, $timeout,
        $window, vcRecaptchaService, httpInterceptor, authService, routing,
        history, webStorage, messenger, paginationConfig, cfpLoadingBar, sidebar
    ) {
        /**
         * The routing service.
         *
         * @type Object
         */
        $scope.routing = routing;

        /**
         * Sidebar service
         *
         * @type Object
         */
        $scope.sidebar = sidebar;

        /**
         * Flag to show modal window for login only once.
         *
         * @type boolean
         */
        $scope.auth = {
            status: true,
            modal: false,
            inprogress: false
        };

        /**
         * Clears the last value for the current path.
         *
         * @param string route The route name.
         */
        $scope.clear = function(route) {
            var url = routing.ngGenerateShort(route);
            history.clear(url);
        };

        /**
         * Updates sidebar status basing on the section.
         *
         * @param string route   The route name.
         * @param string section The section to show.
         */
        $scope.goTo = function(route, section) {
            $scope.clear(route);

            if (!$scope.changing[section] && !$scope.isActive(route)) {
                $scope.changing[section] = 1;
            }

            if ($scope.sidebar.forced) {
                $scope.sidebar.current = 1;
            } else {
                $scope.sidebar.current = $scope.sidebar.wanted;
            }
        };

        /**
         * Removes a class from body and checks if user is authenticated.
         */
        $scope.init = function(language) {
            $translate.use(language);

            paginationConfig.nextText     = $filter('translate')('Next');
            paginationConfig.previousText = $filter('translate')('Previous');
        };

        /**
         * Logs an user in.
         */
        $scope.login = function() {
            $scope.loading = 1;

            var data = {
                _username: $scope.username,
                _password: $scope.password,
                _token:    $scope.token,
            };

            authService.login('manager_ws_auth_check', data, $scope.attempts)
                .then(function (response) {
                    if (response.status == 200) {
                        httpInterceptor.loginConfirmed(response.data);
                        fakeLogin();
                    } else {
                        $scope.token    = response.data.token;
                        $scope.attempts = response.data.attempts;
                        $scope.message  = response.data.message;

                        $scope.loginForm.$setPristine();
                    }

                    $scope.loading = 0;
                }
            );
        };

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
                        return true;
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

                    $scope.user = {};

                    webStorage.local.remove('token');
                    webStorage.local.remove('user');
                }
            });
        };

        /**
         * Force page reload.
         */
        $scope.reload = function() {
            $scope.loading = 1;

            $window.location.reload();
        };

        /**
         * Scrolls the page to top.
         */
        $scope.scrollTop = function() {
            $("body").animate({ scrollTop: 0 }, 250);
        };

        /**
         * Shows the login form when login is required.
         *
         * @param Object event The event object.
         * @param array  args  The list of arguments.
         */
        $scope.$on('auth-login-required', function (event, args) {
            $scope.auth.status = false;
            $scope.loaded      = true;

            webStorage.local.remove('token');
            webStorage.local.remove('user');

            cfpLoadingBar.complete();

            if (!$scope.auth.inprogress) {
                $scope.auth.inprogress = true;

                if ($scope.auth.modal) {
                    var modal = $modal.open({
                        templateUrl: 'modal-login',
                        backdrop: 'static',
                        controller: 'LoginModalCtrl',
                        resolve: {
                            data: function() {
                                var url = routing.generate('manager_ws_auth_login');

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
                    var url = routing.generate('manager_ws_auth_login');

                    $http.post(url).then(function (response) {
                        $scope.token     = response.data.token;
                        $scope.attempts  = response.data.attempts;
                    });
                }
            }
        });

        /**
         * Updates the user and the auth status on event.
         *
         * @param Object event The event object.
         * @param Object args  The user object.
         */
        $scope.$on('auth-login-confirmed', function (event, args) {
            $http.defaults.headers.common.Authorization = 'Bearer ' + args.token;
            $scope.user            = args.user;
            $scope.auth.inprogress = false;
            $scope.auth.modal      = false
            $scope.auth.status     = true;

            webStorage.local.add('token', args.token);
            webStorage.local.add('user', args.user);
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
            if ($location.path().indexOf('framework') != -1) {
                return false;
            }

            history.restore($location.path());
            history.push($location.path(), $location.search());
        });

        /**
         * Restart the loading status for sidebar and check the top margin.
         *
         * @param Object event The event object.
         * @param array  args  The list of arguments.
         */
        $rootScope.$on('$routeChangeSuccess', function (event, next, current) {
            $scope.changing = {};
            $scope.checkFiltersBar();
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
            console.log(args);
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
            sidebar.check();
        });

        /**
         * Updates the content margin-top basing on the filters-navbar height.
         */
        $scope.checkFiltersBar = function checkFiltersBar() {
            $timeout(function() {
                if ($('.view:not(.ng-leave-active) .filters-navbar').length != 1) {
                    return false;
                }

                var margin = 50 + $('.filters-navbar').height() - 15;

                $('.content').css('margin-top', margin + 'px');
            }, 1000);
        };

        webStorage.prefix('ONM-');
        if (webStorage.local.get('token') && webStorage.local.get('user')) {
            $http.defaults.headers.common.Authorization = 'Bearer '
                + webStorage.local.get('token');
            $scope.user = webStorage.local.get('user');
            $scope.loaded = true;
        }
    }
]);
