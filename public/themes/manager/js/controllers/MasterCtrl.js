/**
 * Controller to implement common actions.
 *
 * @param Object $location    The location service.
 * @param Object $scope       The current scope.
 * @param Object fosJsRouting The fosJsRouting service.
 */
angular.module('ManagerApp.controllers').controller('MasterCtrl',
    function ($http, $location, $modal, $scope, vcRecaptchaService, authService, fosJsRouting) {
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
         * Logs in manager.
         */
        $scope.login = function() {
            $scope.loading = true;

            var recaptcha = vcRecaptchaService.data();
            var password = $scope.password;

            if (password.indexOf('md5:') != 0) {
                password = 'md5:' + hex_md5(password);
            }

            var data = {
                _username: $scope.username,
                _password: password,
                _token:    $scope.token,
            }

            if ($scope.attempts > 3) {
                data.reponse = recaptcha.response;
                data.challenge = recaptcha.challenge;
            }

            var url = fosJsRouting.generate('manager_ws_auth_check');

            $http.post(url, data).then(function (response) {
                if (response.data.success) {
                    $scope.auth.status     = true;
                    $scope.auth.inprogress = false;
                    $scope.auth.modal      = true;
                    $scope.user            = response.data.user;

                    authService.loginConfirmed();
                } else {
                    $scope.token    = response.data.token;
                    $scope.attempts = response.data.attempts;
                    $scope.message  = response.data.message;
                }

                $scope.loading = false;
            });
        }

        /**
         * Shows the login form when login is required.
         *
         * @param Object event The event object.
         * @param array  args  The list of arguments.
         */
        $scope.$on('event:auth-loginRequired', function (event, args) {
            $scope.auth.status = false;

            if ($scope.auth.modal && !$scope.auth.inprogress) {
                $scope.auth.inprogress = true;

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

                        authService.loginConfirmed();
                    }
                });
            } else {
                var url = fosJsRouting.generate('manager_ws_auth_login');

                $http.post(url).then(function (response) {
                    $scope.token     = response.data.token;
                    $scope.attempts  = response.data.attempts;
                });

                $scope.auth.inprogress = true;
            }
        });
    }
);
