/**
 * Handles all actions in users listing.
 *
 * @param  Object $scope      The current scope.
 * @param  Object itemService The item service.
 * @param  Object data        The input data.
 *
 * @return Object The command controller.
 */
angular.module('ManagerApp.controllers').controller('UserCtrl',
    function ($location, $scope, itemService, fosJsRouting, messenger, data) {
        /**
         * List of available users.
         *
         * @type Object
         */
        $scope.user = {
            id_user_group: []
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

            itemService.update('manager_ws_user_update', $scope.user.id,
                $scope.user).then(function (response) {
                    messenger.post({
                        message: response.data.message.text,
                        type:    response.data.message.type
                    });

                    $scope.saving = 0;
                });
        };

        if (data.data) {
            $scope.user = data.data
        }
    }
);
