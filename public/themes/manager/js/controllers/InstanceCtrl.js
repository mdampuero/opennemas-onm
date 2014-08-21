
angular.module('ManagerApp.controllers').controller('InstanceCtrl',
    function ($location, $scope, itemService, fosJsRouting, messenger, data) {
        /**
         * The instance object.
         *
         * @type Object
         */
        $scope.instance = {
            domains: [],
            settings: {
                template: 'base'
            },
            external: {
                site_language: 'es_ES',
                pass_level:    -1,
                max_mailing:   0,
                time_zone:     '335'
            }
        };

        /**
         * The template parameters.
         *
         * @type Object
         */
        $scope.template = data.template;

        /**
         * Adds a new domain to the instance.
         */
        $scope.addDomain = function() {
            if ($scope.instance.domains.indexOf($scope.new_domain) == -1) {
                $scope.instance.domains.push($scope.new_domain);
            }
        };

        /**
         * Removes an instance domain.
         *
         * @param integer index The index of the domain to remove.
         */
        $scope.removeDomain = function(index) {
            $scope.instance.domains.splice(index, 1);
        };

        /**
         * Creates a new instance.
         */
        $scope.save = function() {
            $scope.saving = 1;

            itemService.save('manager_ws_instance_create', $scope.instance)
                .then(function (response) {
                    if (response.data.success) {
                        $location.path(fosJsRouting.ngGenerateShort('/manager',
                            'manager_instance_show',
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
         * Updates an instance.
         */
        $scope.update = function() {
            $scope.saving = 1;

            itemService.update('manager_ws_instance_update', $scope.instance.id,
                $scope.instance).then(function (response) {
                    messenger.post({
                        message: response.data.message.text,
                        type:    response.data.message.type
                    });

                    $scope.saving = 0;
                });
        };

        // Initialize instance
        if (data.instance) {
            $scope.instance = data.instance;
        }
    }
);
