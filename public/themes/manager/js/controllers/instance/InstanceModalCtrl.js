
angular.module('ManagerApp.controllers').controller('InstanceModalCtrl', [
    '$modalInstance', '$scope', 'itemService', 'messenger', 'selected', 'template',
    function ($modalInstance, $scope, itemService, messenger, selected, template) {
        /**
         * Flag to indicate if multiple items are selected
         *
         * @type boolean
         */
        $scope.multiple = angular.isArray(selected);

        /**
         * The instance object.
         *
         * @type Object
         */
        $scope.selected = {
            all: true,
            modules: selected
        }

        /**
         * The template parameters.
         *
         * @type Object
         */
        $scope.template = template;

        /**
         * Closes the current modal
         */
        $scope.accept = function() {
            $modalInstance.close($scope.selected.modules);
        };

        /**
         * Closes the current modal and returns the selected items.
         */
        $scope.allSelected = function() {
            for (var key in $scope.template.available_modules) {
                if (!$scope.selected.modules
                        || $scope.selected.modules.indexOf(key) == -1) {
                    $scope.selected.all = 0;
                    return false
                }
            }

            return true;
        }

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

            itemService.delete('manager_ws_instance_delete', selected.id)
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
         * Deletes the selected instances.
         */
        $scope.deleteSelected = function() {
            $scope.deleting = true;

            itemService.deleteSelected('manager_ws_instances_delete', selected)
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

        /**
         * Selects/unselects all modules.
         */
        $scope.selectAll = function() {
            if ($scope.selected.all) {
                for (var key in $scope.template.available_modules) {
                    if ($scope.selected.modules.indexOf(key) == -1) {
                        $scope.selected.modules.push(key);
                    }
                }
            } else {
                $scope.selected.modules = [];
            }
        };


        // Split modules in columns
        if ($scope.template.available_modules) {
            $scope.modules = [];
            var i = 0;
            var count = 0;
            for (var key in $scope.template.available_modules) {
                if (!$scope.modules[i]) {
                    $scope.modules[i] = {};
                }

                $scope.modules[i][key] = $scope.template.available_modules[key];
                count++;

                if (count > 12) {
                    count = 0;
                    i++;
                }
            };
        }

        $scope.$on('$destroy', function() {
            $scope.multiple = null;
            $scope.selected = null;
            $scope.template = null;
        });
    }
]);
