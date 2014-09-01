
angular.module('ManagerApp.controllers').controller('UserGroupModalCtrl',
    function ($modalInstance, $scope, itemService, messenger, selected) {
        /**
         * The instance object.
         *
         * @type Object
         */
        $scope.selected = selected;

        /**
         * Flag to indicate if multiple items are selected
         *
         * @type boolean
         */
        $scope.multiple = angular.isArray(selected);

        /**
         * Closes the current modal
         */
        $scope.close = function() {
            $modalInstance.dismiss();
        };

        /**
         * Deletes an instance.
         */
        $scope.delete = function() {
            $scope.deleting = true;

            itemService.delete('manager_ws_user_group_delete', selected.id)
                .then(function (response) {
                    if (response.data.message) {
                        messenger.post({
                            message: response.data.message.text,
                            type:    response.data.message.type
                        });
                    };


                    if (response.data.success) {
                        $modalInstance.close(response.data.success);
                    }

                    $scope.deleting = false;
                });
        }

        /**
         * Deletes the selected groups.
         */
        $scope.deleteSelected = function() {
            $scope.deleting = true;

            itemService.deleteSelected('manager_ws_user_groups_delete', selected)
                .then(function(response) {
                    for (var i = 0; i < response.data.messages.length; i++) {
                        messenger.post({
                            message: response.data.messages[i].text,
                            type:    response.data.messages[i].type
                        });
                    };

                    if (response.data.success) {
                        $modalInstance.close(response.data.success);
                    }

                    $scope.deleting = false;
                });
        }

        $scope.$on('$destroy', function() {
            $scope.multiple = null;
            $scope.selected = null;
        });
    }
);
