
angular.module('ManagerApp.controllers').controller('InstanceCtrl', [
    '$filter', '$location', '$modal', '$scope', 'itemService', 'routing', 'messenger', 'data',
    function ($filter, $location, $modal, $scope, itemService, routing, messenger, data) {
        /**
         * The instance object.
         *
         * @type Object
         */
        $scope.instance = {
            domains: [],
            activated_modules: [],
            changes_in_modules: [],
            support_plan: 'SUPPORT_NONE',
            settings: {
                TEMPLATE_USER: 'base'
            },
            external: {
                site_language: 'es_ES',
                pass_level:    -1,
                max_mailing:   0,
                max_users:   0,
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
         * Copy of changed_in_modules array.
         *
         * @type Array
         */
        if (data.instance) {
            $scope.changed_modules = angular.copy(data.instance.changes_in_modules);
        } else {
            $scope.changed_modules = '';
        }


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
                        if ($scope.instance.activated_modules.indexOf(module.id) === -1) {
                            $scope.instance.activated_modules.push(module.id);
                        }
                    } else {
                        $scope.instance.activated_modules.splice(
                            $scope.instance.activated_modules.indexOf(module.id),
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
                    if ($scope.instance.activated_modules.indexOf(module.id) === -1) {
                        return false;
                    }
                }
            }

            return true;
        }

        /**
         * Add/remove modules from changed_in_modules array.
         *
         * @param string  moduleId The id of the module.
         *
         */
        $scope.toggleChanges = function(module) {
            if ($scope.instance.changes_in_modules.indexOf(module.id) != -1) {
                $scope.instance.changes_in_modules.splice(
                    $scope.instance.changes_in_modules.indexOf(module.id),
                    1
                );
            } else if ($scope.changed_modules.indexOf(module.id) != -1 &&
                $scope.instance.changes_in_modules.indexOf(module.id) == -1
            ) {
                $scope.instance.changes_in_modules.push(module.id);
            }
        }

        /**
         * Initialize support plan
         */
        $scope.initializeSupportPlan = function() {
            if ($scope.instance.support_plan.indexOf('SUPPORT') == -1) {
                $scope.instance.support_plan = 'SUPPORT_NONE';
            }
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
            if ($scope.instanceForm.$invalid) {
                $scope.formValidated = 1;

                messenger.post({
                    message: $filter('translate')('FormErrors'),
                    type:    'error'
                });

                return false;
            }

            $scope.saving = 1;

            if ($scope.instance.domain_expire && angular.isObject($scope.instance.domain_expire)) {
                $scope.instance.domain_expire = $scope.instance.domain_expire.toString();
            }

            if ($scope.instance.external_last_invoice && angular.isObject($scope.instance.domain_expire)) {
                $scope.instance.external_last_invoice = $scope.instance.external_last_invoice.toString();
            }

            itemService.save('manager_ws_instance_create', $scope.instance)
                .then(function (response) {
                    messenger.post({
                        message: response.data,
                        type: response.status == 201  ? 'success' : 'error'
                    });

                    if (response.status == 201) {
                        // Get new instance id
                        var url = response.headers()['location'];
                        var id  = url.substr(url.lastIndexOf('/') + 1);

                        url = routing.ngGenerateShort(
                            'manager_instance_show', { id: id });
                        $location.path(url);
                    }

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

                    if ($scope.instance.activated_modules.indexOf(module.id) === -1) {
                        $scope.instance.activated_modules.push(module.id);
                    }
                }
            } else {
                $scope.selected.all = false;
                $scope.instance.activated_modules = [];
            }
        };

        /**
         * Updates an instance.
         */
        $scope.update = function() {
            if ($scope.instanceForm.$invalid) {
                $scope.formValidated = 1;

                messenger.post({
                    message: $filter('translate')('FormErrors'),
                    type:    'error'
                });

                return false;
            }

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
                        message: response.data,
                        type: response.status == 200 ? 'success' : 'error'
                    });

                    $scope.saving = 0;
                });
        };

        if (data.instance) {
            // Initialize instance
            $scope.instance = data.instance;
        } else {
            // Select Base plan as default
            for (var i = 0; i < data.template.available_modules.length; i++) {
                if (data.template.available_modules[i].plan == 'Base') {
                    $scope.instance.activated_modules.push(
                        data.template.available_modules[i].id);
                }
            };
        }

        $scope.$on('$destroy', function() {
            $scope.instance = null;
            $scope.changed_modules = null;
            $scope.template = null;
            $scope.selected = null;
        })

        /**
         * Forces the values to be integer.
         *
         * @param Object newValues New values.
         * @param Object oldValues Old values.
         */
        $scope.$watch(
            '[instance.external.max_users, instance.external.max_mailing]',
            function(newValues, oldValues) {
                $scope.instance.external.max_users = parseInt($scope.instance.external.max_users);
                $scope.instance.external.max_mailing = parseInt($scope.instance.external.max_mailing);
            },
            true
        );

        // Initializes the selected flags
        for (var i = 0; i < $scope.template.plans.length; i++) {
            var plan = $scope.template.plans[i];
            var modulesInPlan = $filter('filter')($scope.template.available_modules, { plan: plan });
            $scope.selected.plan[plan] = true;

            for (var j = 0; j < modulesInPlan.length; j++) {
              if ($scope.instance.activated_modules.indexOf(modulesInPlan[j].id) === -1) {
                $scope.selected.plan[plan] = false;
              }
            }
        }
    }
]);
