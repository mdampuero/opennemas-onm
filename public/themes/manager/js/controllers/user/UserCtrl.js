'use strict';

/**
 * Handles all actions in users listing.
 *
 * @param  Object $scope      The current scope.
 * @param  Object itemService The item service.
 * @param  Object data        The input data.
 *
 * @return Object The command controller.
 */
angular.module('ManagerApp.controllers').controller('UserCtrl', [
    '$filter', '$location', '$scope', 'itemService', 'routing', 'messenger', 'data',
    function ($filter, $location, $scope, itemService, routing, messenger, data) {
        /**
         * List of available users.
         *
         * @type Object
         */
        $scope.user = {
            meta: {
                user_language: 'default'
            }
        };

        /**
         * The template parameters.
         *
         * @type Object
         */
        $scope.template = data.template;

        /**
         * Creates a new user.
         */
        $scope.save = function() {
            if ($scope.userForm.$invalid) {
                $scope.formValidated = 1;

                messenger.post({
                    message: $filter('translate')('FormErrors'),
                    type:    'error'
                });

                return false;
            }

            $scope.saving = 1;

            if ($scope.user.meta.paywall_time_limit) {
                $scope.user.meta.paywall_time_limit = $scope.user.meta.paywall_time_limit.toString();
            }

            itemService.save('manager_ws_user_create', $scope.user)
                .then(function (response) {
                    messenger.post({
                        message: response.data,
                        type: response.status === 201  ? 'success' : 'error'
                    });

                    if (response.status === 201) {
                        // Get new user id
                        var url = response.headers()['location'];
                        var id  = url.substr(url.lastIndexOf('/') + 1);

                        url = routing.ngGenerateShort(
                            'manager_user_show', { id: id });
                        $location.path(url);
                    }

                    $scope.saving = 0;
                });
        };

        /**
         * Updates an user.
         */
        $scope.update = function() {
            if ($scope.userForm.$invalid) {
                $scope.formValidated = 1;

                messenger.post({
                    message: $filter('translate')('FormErrors'),
                    type:    'error'
                });

                return false;
            }

            $scope.saving = 1;

            if ($scope.user.meta.paywall_time_limit) {
                $scope.user.meta.paywall_time_limit = $scope.user.meta.paywall_time_limit.toString();
            }

            itemService.update('manager_ws_user_update', $scope.user.id,
                $scope.user).then(function (response) {
                    messenger.post({
                        message: response.data,
                        type: response.status === 200 ? 'success' : 'error'
                    });

                    $scope.saving = 0;
                });
        };

        //  Initialize user
        if (data.data) {
            $scope.user = data.data;

            if (angular.isArray($scope.user.meta)) {
                $scope.user.meta = {
                    user_language: 'default'
                };
            }
        }

        $scope.$on('$destroy', function() {
            $scope.user     = null;
            $scope.template = null;
        });
    }
]);
