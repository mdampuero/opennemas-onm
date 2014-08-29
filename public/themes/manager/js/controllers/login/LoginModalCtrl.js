
angular.module('ManagerApp.controllers').controller('LoginModalCtrl',
    function ($http, $modalInstance, $scope, fosJsRouting, data) {
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
         * Closes the current modal
         */
        $scope.close = function() {
            $modalInstance.close({ success: false });
        };

        /**
         * Logs in manager.
         */
        $scope.login = function() {
            $scope.loading = 1;

            var password = $scope.user.password;

            if (password.indexOf('md5:') === -1) {
                password = 'md5:' + hex_md5(password);
            }

            var data = {
                _username: $scope.user.username,
                _password: password,
                _token:    $scope.user.token,
            }

            if ($scope.attempts > 3) {
                recaptcha = vcRecaptchaService.data();
                data.reponse = recaptcha.response;
                data.challenge = recaptcha.challenge;
            }

            var url = fosJsRouting.generate('manager_ws_auth_check');

            $http.post(url, data).then(function (response) {
                if (response.data.success) {
                    $modalInstance.close({
                        success: true,
                        user: response.data.user
                    });
                } else {
                    $scope.user.token = response.data.token;
                    $scope.attempts   = response.data.attempts;
                    $scope.message    = response.data.message;
                }

                $scope.loading = 0;
            });
        }
    }
);
