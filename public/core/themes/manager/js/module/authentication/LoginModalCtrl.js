'use strict';

angular.module('ManagerApp.controllers').controller('LoginModalCtrl', [
    '$http', '$uibModalInstance', '$scope', 'authService', 'routing', 'vcRecaptchaService', 'data',
    function ($http, $uibModalInstance, $scope, authService, routing, vcRecaptchaService, data) {
        /**
         * Login attempts
         *
         * @type integer
         */
        $scope.attempts = data.attempts;

        /**
         * The authentication value.
         *
         * @type Object
         */
        $scope.user = {
            token: data.token
        };

        /**
         * Closes the current modal.
         */
        $scope.close = function() {
            $uibModalInstance.close({ success: false });
        };

        /**
         * Logs user in.
         */
        $scope.login = function() {
            $scope.loading = 1;

            var data = {
                _username: $scope.user.username,
                _password: $scope.user.password,
                _token:    $scope.user.token,
            };

            authService.login('manager_ws_auth_check', data,$scope.attempts)
                .then(function (response) {
                    if (response.data.success) {
                        $uibModalInstance.close({
                            success: true,
                            user: response.data.user
                        });
                    } else {
                        $scope.message    = response.data.message;
                        $scope.user.token = response.data.token;
                        $scope.attempts   = response.data.attempts;
                    }

                    $scope.loading = 0;
                });
        };
    }
]);
