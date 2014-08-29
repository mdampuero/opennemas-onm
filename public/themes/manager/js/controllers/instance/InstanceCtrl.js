
angular.module('ManagerApp.controllers').controller('InstanceCtrl',
    function ($location, $modal, $scope, itemService, fosJsRouting, messenger, data) {
        /**
         * The instance object.
         *
         * @type Object
         */
        $scope.instance = {
            domains: [],
            settings: {
                TEMPLATE_USER: 'base'
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
         * The instance object.
         *
         * @type Object
         */
        $scope.selected_modules = {
            all: true,
            modules: $scope.instance.external.activated_modules
        }

        $scope.togglePlan = function(planName) {
            for (var module in $scope.template.available_modules) {
                module = $scope.template.available_modules[module]
                if (module.plan == planName) {
                    console.log(planName, $scope.instance.external.activated_modules.indexOf(module.id));
                    $scope.instance.external.activated_modules.push(module.id);
                };
            };
        }

        $scope.isPlanChecked = function(planName) {
            // console.log(planName, $scope.template)
            // var has_all_modules = true;
            for (var module in $scope.template.modules) {
                console.log(planName, $scope.instance.external.activated_modules.contains(module.id))
                if (module.plan == planName) {
                    if (!$scope.instance.external.activated_modules.contains(module.id)) {
                        return false;
                    }
                    //  else {
                    //     has_all_modules = has_all_modules && true
                    // }
                };
            }

            return true;
        }

        /**
         * Adds a new domain to the instance.
         */
        $scope.addDomain = function() {
            if ($scope.instance.domains.indexOf($scope.new_domain) == -1 &&
                $scope.instance.domains.indexOf($scope.new_domain) !== ''
            ) {
                $scope.instance.domains.push($scope.new_domain);
                $scope.new_domain = '';
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

            $scope.instance.external.domain_expire = $scope.instance.external.domain_expire.toString();
            $scope.instance.external.last_invoice  = $scope.instance.external.last_invoice.toString();

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

            $scope.instance.external.domain_expire = $scope.instance.external.domain_expire.toString();
            $scope.instance.external.last_invoice  = $scope.instance.external.last_invoice.toString();

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
