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
    '$location', '$scope', 'itemService', 'fosJsRouting', 'messenger', 'data',
    function ($location, $scope, itemService, fosJsRouting, messenger, data) {
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
            $scope.saving = 1;

            if ($scope.user.meta.paywall_time_limit) {
                $scope.user.meta.paywall_time_limit = $scope.user.meta.paywall_time_limit.toString();
            }

            itemService.save('manager_ws_user_create', $scope.user)
                .then(function (response) {
                    if (response.data.success) {
                        $location.path(fosJsRouting.ngGenerateShort('/manager',
                            'manager_user_show',
                            { id: response.data.message.id }));
                    }

                    messenger.post({
                        message: response.data.message.text,
                        type:    response.data.message.type
                    });

                    $scope.saving = 0;
                });
        };

        /**
         * Updates an user.
         */
        $scope.update = function() {
            $scope.saving = 1;

            if ($scope.user.meta.paywall_time_limit) {
                $scope.user.meta.paywall_time_limit = $scope.user.meta.paywall_time_limit.toString();
            }

            itemService.update('manager_ws_user_update', $scope.user.id,
                $scope.user).then(function (response) {
                    messenger.post({
                        message: response.data.message.text,
                        type:    response.data.message.type
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
        })
    }
]);
