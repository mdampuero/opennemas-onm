/**
 * Handles all actions in commands listing.
 *
 * @param  Object $scope      The current scope.
 * @param  Object itemService The item service.
 * @param  Object data        The input data.
 *
 * @return Object The command controller.
 */
angular.module('ManagerApp.controllers').controller('CommandListCtrl',
    function ($scope, itemService, data) {
        /**
         * List of available commands.
         *
         * @type Object
         */
        $scope.commands = data.results;

        /**
         * List of instances (to clear smarty-cache)
         *
         * @type Object
         */
        $scope.template = data.template;


        $scope.executeCommand = function(command_name, data) {

            $scope.saving = 1;

            itemService.executeCommand('manager_ws_command_execute', command_name, data)
                .then(function(response) {
                    if (response.data.success) {
                        $scope.command_name = response.data.name;
                        $scope.command_output = response.data.output;

                        $location.path(fosJsRouting.ngGenerateShort('/manager',
                            'manager_command_output'));
                    }

                    $scope.saving = 0;
                })
        }

        $scope.commandOutput = function(command_name, data) {


        }
    }
);
