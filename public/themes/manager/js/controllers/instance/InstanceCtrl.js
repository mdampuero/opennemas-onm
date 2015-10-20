(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  InstanceCtrl
     *
     * @requires $filter
     * @requires $location
     * @requires $modal
     * @requires $scope
     * @requires itemService
     * @requires routing
     * @requires messenger
     * @requires data
     *
     * @description
     *   Handles actions for instance edition form
     */
    .controller('InstanceCtrl', [
      '$filter', '$location', '$modal', '$scope', 'itemService', 'routing', 'messenger', 'data',
      function ($filter, $location, $modal, $scope, itemService, routing, messenger, data) {
        /**
         * @memberOf InstanceCtrl
         *
         * @description
         *   The instance object.
         *
         * @type {Object}
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
          },
          metas: {
            billing: []
          }
        };

        /**
         * @memberOf InstanceCtrl
         *
         * @description
         *   The template parameters.
         *
         * @type {Object}
         */
        $scope.template = data.template;

        /**
         * @memberOf InstanceCtrl
         *
         * @description
         *   The instance object.
         *
         * @type {Object}
         */
        $scope.selected = {
          all: false,
          plan: {}
        };

        /**
         * @function addDomain
         * @memberOf InstanceCtrl
         *
         * @description
         *   Adds a new domain to the instance.
         */
        $scope.addDomain = function() {
          if ($scope.instance.domains.indexOf($scope.new_domain) === -1 &&
              $scope.instance.domains.indexOf($scope.new_domain) !== '') {
            $scope.instance.domains.push($scope.new_domain);
            $scope.new_domain = '';
          }
        };

        /**
         * @function initializeSupportPlan
         * @memberOf InstanceCtrl
         *
         * @description
         *   Initialize support plan
         */
        $scope.initializeSupportPlan = function() {
          if ($scope.instance.support_plan.indexOf('SUPPORT') === -1) {
            $scope.instance.support_plan = 'SUPPORT_NONE';
          }
        };

        /**
         * @function isPlanSelected
         * @memberOf InstanceCtrl
         *
         * @description
         *   Checks if all modules of the plan are selected.
         *
         * @param string  plan The plan to check.
         *
         * @return boolean True if all modules of the plan are selected.
         *                 Otherwise, return false.
         */
        $scope.isPlanSelected = function(plan) {
          for (var module in $scope.template.available_modules) {
            module = $scope.template.available_modules[module];
            if (module.plan === plan) {
              if ($scope.instance.activated_modules.indexOf(module.id) === -1) {
                return false;
              }
            }
          }

          return true;
        };

        /**
         * @function removeDomain
         * @memberOf InstanceCtrl
         *
         * @description
         *   Removes an instance domain.
         *
         * @param integer index The index of the domain to remove.
         */
        $scope.removeDomain = function(index) {
          $scope.instance.domains.splice(index, 1);
        };

        /**
         * @function save
         * @memberOf InstanceCtrl
         *
         * @description
         *   Creates a new instance.
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

          if ($scope.instance.external.last_invoice && angular.isObject($scope.instance.external.last_invoice)) {
            $scope.instance.external.last_invoice = $scope.instance.external.last_invoice.toString();
          }

          itemService.save('manager_ws_instance_create', $scope.instance)
            .success(function (response) {
              messenger.post({ message: response, type: 'success' });

              if (response.status === 201) {
                // Get new instance id
                var url = response.headers()['location'];
                var id  = url.substr(url.lastIndexOf('/') + 1);

                url = routing.ngGenerateShort(
                  'manager_instance_show', { id: id });
                $location.path(url);
              }

              $scope.saving = 0;
            }).error(function(response) {
              $scope.saving = 0;
              messenger.post({ message: response, type: 'error' });
            });
        };

        /**
         * @function selectAll
         * @memberOf InstanceCtrl
         *
         * @description
         *   Selects/unselects all modules.
         */
        $scope.selectAll = function() {
          if (!$scope.selected.all) {
            $scope.selected.all = true;

            // Add modules to instance
            for (var module in $scope.template.available_modules) {
              module = $scope.template.available_modules[module];

              if (module.plan !== 'Support' &&
                  $scope.instance.activated_modules.indexOf(module.id) === -1) {
                $scope.instance.activated_modules.push(module.id);
              }
            }

            // Update selected flag for each plan
            for (var i = 0; i < $scope.template.plans.length; i++) {
              $scope.selected.plan[$scope.template.plans[i]] = true;
            }
          } else {
            $scope.selected.all = false;
            $scope.selected.plan = {};
            $scope.instance.activated_modules = [];
          }

          $scope.updateSupport($scope.instance.support_plan);
        };

        /**
         * @function toggleChanges
         * @memberOf InstanceCtrl
         *
         * @description
         *   Add/remove modules from changed_in_modules array.
         *
         * @param string  moduleId The id of the module.
         */
        $scope.toggleChanges = function(module) {
          if ($scope.instance.changes_in_modules.indexOf(module.id) !== -1) {
            $scope.instance.changes_in_modules.splice(
              $scope.instance.changes_in_modules.indexOf(module.id),
              1
              );
          } else if ($scope.changed_modules.indexOf(module.id) !== -1 &&
            $scope.instance.changes_in_modules.indexOf(module.id) === -1
            ) {
            $scope.instance.changes_in_modules.push(module.id);
          }
        };

        /**
         * @function togglePlan
         * @memberOf InstanceCtrl
         *
         * @description
         *   Selects/unselects all modules of the plan.
         *
         * @param string plan The selected plan.
         */
        $scope.togglePlan = function(plan) {
          for (var module in $scope.template.available_modules) {
            module = $scope.template.available_modules[module];
            if (module.plan === plan) {
              if ($scope.selected.plan[plan]) {
                if ($scope.instance.activated_modules.indexOf(module.id) === -1) {
                  $scope.instance.activated_modules.push(module.id);
                }
              } else {
                $scope.instance.activated_modules.splice(
                  $scope.instance.activated_modules.indexOf(module.id), 1);
              }
            }
          }
        };

        /**
         * @function update
         * @memberOf InstanceCtrl
         *
         * @description
         *   Updates an instance.
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

          if ($scope.instance.external.last_invoice && angular.isObject($scope.instance.external.last_invoice)) {
            $scope.instance.external.last_invoice = $scope.instance.external.last_invoice.toString();
          }

          itemService.update('manager_ws_instance_update', $scope.instance.id,
            $scope.instance).success(function (response) {
              messenger.post({ message: response, type: 'success' });
              $scope.saving = 0;
            }).error(function(response) {
              messenger.post({ message: response, type: 'error' });
              $scope.saving = 0;
            });
          };

          if (data.instance) {
            // Initialize instance
            $scope.instance = data.instance;

            if (!$scope.instance.metas) {
              $scope.instance.metas = {};
            }
          } else {
            // Select Base plan as default
            for (var i = 0; i < data.template.available_modules.length; i++) {
              if (data.template.available_modules[i].plan == 'Base') {
                $scope.instance.activated_modules.push(
                  data.template.available_modules[i].id);
              }
            }
          }

          $scope.$on('$destroy', function() {
            $scope.instance = null;
            $scope.changed_modules = null;
            $scope.template = null;
            $scope.selected = null;
          });

        /**
         * @function updateSupport
         * @memberOf InstanceCtrl
         *
         * @description
         *   Updates activated modules when support plan changes.
         *
         * @param {String} id The support plan id.
         *
         */
        $scope.updateSupport = function(id) {
          for (var i = 0; i < data.template.available_modules.length; i++) {
            var module = data.template.available_modules[i];

            if (module.plan === 'Support') {
              var index = $scope.instance.activated_modules.indexOf(module.id);

              if (index !== -1) {
                $scope.instance.activated_modules.splice(index, 1);
              }
            }
          }

          $scope.instance.activated_modules.push(id);
        };

        // Forces values to be integer.
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
})();
