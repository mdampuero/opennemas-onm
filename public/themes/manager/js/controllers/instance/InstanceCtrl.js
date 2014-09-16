
angular.module('ManagerApp.controllers').controller('InstanceCtrl', [
    '$location', '$modal', '$scope', 'itemService', 'fosJsRouting', 'messenger', 'data',
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
                activated_modules: [],
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
        $scope.selected = {
            all: false,
            plan: {}
        };

        /**
         * Selects/unselects all modules of the plan.
         *
         * @param string plan The selected plan.
         */
        $scope.togglePlan = function(plan) {
            for (var module in $scope.template.available_modules) {
                module = $scope.template.available_modules[module]
                if (module.plan == plan) {
                    if ($scope.selected.plan[plan]) {
                        if ($scope.instance.external.activated_modules.indexOf(module.id) === -1) {
                            $scope.instance.external.activated_modules.push(module.id);
                        }
                    } else {
                        $scope.instance.external.activated_modules.splice(
                            $scope.instance.external.activated_modules.indexOf(module.id),
                            1
                        );
                    }
                }
            }
        };

        /**
         * Checks if all modules of the plan are selected.
         *
         * @param string  plan The plan to check.
         *
         * @return boolean True if all modules of the plan are selected.
         *                 Otherwise, return false.
         */
        $scope.isPlanSelected = function(plan) {
            for (var module in $scope.template.available_modules) {
                module = $scope.template.available_modules[module]
                if (module.plan == plan) {
                    if ($scope.instance.external.activated_modules.indexOf(module.id) === -1) {
                        return false;
                    }
                }
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

            if ($scope.instance.domain_expire && angular.isObject($scope.instance.domain_expire)) {
                $scope.instance.domain_expire = $scope.instance.domain_expire.toString();
            }

            if ($scope.instance.external_last_invoice && angular.isObject($scope.instance.domain_expire)) {
                $scope.instance.external_last_invoice = $scope.instance.external_last_invoice.toString();
            }

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
         * Selects/unselects all modules.
         */
        $scope.selectAll = function() {
            if (!$scope.selected.all) {
                $scope.selected.all = true;
                for (var module in $scope.template.available_modules) {
                    module = $scope.template.available_modules[module];

                    if ($scope.instance.external.activated_modules.indexOf(module.id) === -1) {
                        $scope.instance.external.activated_modules.push(module.id);
                    }
                }
            } else {
                $scope.selected.all = false;
                $scope.instance.external.activated_modules = [];
            }
        };

        /**
         * Updates an instance.
         */
        $scope.update = function() {
            $scope.saving = 1;

            if ($scope.instance.domain_expire && angular.isObject($scope.instance.domain_expire)) {
                $scope.instance.domain_expire = $scope.instance.domain_expire.toString();
            }

            if ($scope.instance.external_last_invoice && angular.isObject($scope.instance.domain_expire)) {
                $scope.instance.external_last_invoice = $scope.instance.external_last_invoice.toString();
            }

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

        $scope.$on('$destroy', function() {
            $scope.instance = null;
            $scope.template = null;
            $scope.selected = null;
        })
    }
]);
