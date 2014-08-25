/**
 * Handles all actions in user groups listing.
 *
 * @param  Object $scope      The current scope.
 * @param  Object itemService The item service.
 * @param  Object data        The input data.
 *
 * @return Object The command controller.
 */
angular.module('ManagerApp.controllers').controller('UserGroupCtrl',
    function ($location, $scope, itemService, fosJsRouting, messenger, data) {
        /**
         * List of available groups.
         *
         * @type Object
         */
        $scope.group = {
            privileges: []
        };

        /**
         * The template parameters.
         *
         * @type Object
         */
        $scope.template = data.template;

        /**
         * Creates a new user group.
         */
        $scope.save = function() {
            $scope.saving = 1;

            itemService.save('manager_ws_user_group_create', $scope.group)
                .then(function (response) {
                    if (response.data.success) {
                        $location.path(fosJsRouting.ngGenerateShort('/manager',
                            'manager_user_group_show',
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
         * Updates an user group.
         */
        $scope.update = function() {
            $scope.saving = 1;

            itemService.update('manager_ws_user_group_update', $scope.group.id,
                $scope.group).then(function (response) {
                    messenger.post({
                        message: response.data.message.text,
                        type:    response.data.message.type
                    });

                    $scope.saving = 0;
                });
        };

        // Initialize group
        if (data.group) {
            $scope.group = data.group;
        }
    }
);
