
angular.module('ManagerApp.controllers').controller('LoginModalCtrl',
    function ($modalInstance, $scope) {
        /**
         * Closes the current modal
         */
        $scope.close = function() {
            $modalInstance.dismiss();
        };

        /**
         * Logs in manager.
         */
        $scope.login = function() {
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
                recaptcha = vcRecaptchaService.data();
                data.reponse = recaptcha.response;
                data.challenge = recaptcha.challenge;
            }

            var url = fosJsRouting.generate('manager_ws_auth_check');

            $http.post(url, data).then(function (response) {
                if (response.data.success) {
                    $scope.auth.status = true;
                    $scope.auth.inprogress = false;
                    $scope.auth.modal = true;

                    authService.loginConfirmed();
                } else {
                    $scope.token    = response.data.token;
                    $scope.attempts = response.data.attempts;
                }
            });
        }
    }
);
