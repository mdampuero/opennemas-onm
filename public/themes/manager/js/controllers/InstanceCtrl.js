
angular.module('ManagerApp.controllers').controller('InstanceCtrl',
    function ($scope, itemService, fosJsRouting, data) {
        /**
         * The instance object.
         *
         * @type Object
         */
        $scope.instance = data.data;

        /**
         * The template parameters.
         *
         * @type Object
         */
        $scope.template = data.template;

        /**
         * Creates a new instance.
         */
        $scope.save = function() {
            itemService.save('manager_ws_instance_create', $scope.instance)
                .then(function (response) {
                    console.log(response);
                });
        }
    }
);
