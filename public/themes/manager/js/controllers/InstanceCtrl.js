
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
         * Variable to handle domains from tags input.
         *
         * @type Object
         */
        $scope.domains = angular.copy($scope.instance.domains);

        /**
         * Cleans domains from tags input and update instance.
         */
        $scope.cleanDomains = function () {
            var domains = [];
            angular.forEach($scope.domains, function(domain){
                domains.push(domain.text);
            });

            if (domains.length < $scope.instance.main_domain) {
                $scope.instance.main_domain = 0;
            }

            $scope.instance.domains = domains;
        };

        /**
         * Creates a new instance.
         */
        $scope.save = function() {
            itemService.save('manager_ws_instance_create', $scope.instance)
                .then(function (response) {
                    console.log(response);
                });
        };

        /**
         * Updates an instance.
         */
        $scope.update = function() {
            itemService.update('manager_ws_instance_update',
                { id: $scope.instance.id }, $scope.instance)
                .then(function (response) {
                    console.log(response);
                });
        }
    }
);
