(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  AccountCtrl
     *
     * @requires $filter
     * @requires $scope
     *
     * @description
     *   Controller to handle actions in my account.
     *
     * @requires $scope
     */
    .controller('AccountCtrl', ['$filter', '$scope',
      function($filter, $scope) {
        /**
         * @memberOf AccountCtrl
         *
         * @description
         *   The object for selected all flags
         *
         * @type {Object}
         */
        $scope.selected = {};


        $scope.init = function(instance, plans, modules) {
          $scope.instance = instance;
          $scope.plans    = plans;
          $scope.modules  = modules;
        };

        /**
         * @function changed
         * @memberOf AccountCtrl
         *
         * @description
         *   Checks if there are changes in the selected modules array.
         *
         * @param {String} plan The name of the plan.
         */
        $scope.changed = function() {
          if ($scope.changes.length !== $scope.instance.activated_modules.length) {
            return true;
          }

          for (var i = 0; i < $scope.instance.activated_modules.length; i++) {
            var module = $scope.instance.activated_modules[i];

            if ($scope.changes.indexOf(module) === -1) {
              return true;
            }
          }

          return false;
        };

        /**
         * @function areAllBlocked
         * @memberOf AccountCtrl
         *
         * @description
         *   Checks if all modules from a plan are waiting for activation or
         *   deactivation.
         *
         * @param {String} plan The name of the plan.
         *
         * @return {Boolean} True if all modules are waiting for activation or
         *                   deactivation.
         */
        $scope.isBlocked = function(plan) {
          var modules = $filter('filter')($scope.modules, { plan: plan });

          for (var i = 0; i < modules.length; i++) {
            var module = modules[i].id;

            if ($scope.instance.changes_in_modules.indexOf(module) === -1) {
              return false;
            }
          }

          return true;
        };

        /**
         * @function isDowngraded
         * @memberOf AccountCtrl
         *
         * @description
         *   Checks if a module is waiting for deactivation.
         *
         * @param {String} module The name of the module.
         */
        $scope.isDowngraded = function(module) {
          return $scope.changes.indexOf(module) === -1 &&
            $scope.instance.changes_in_modules.indexOf(module) !== -1;
        };

        /**
         * @function isUpgraded
         * @memberOf AccountCtrl
         *
         * @description
         *   Checks if a module is waiting for activation.
         *
         * @param {String} module The name of the module.
         */
        $scope.isUpgraded = function(module) {
          return $scope.changes.indexOf(module) !== -1 &&
            $scope.instance.changes_in_modules.indexOf(module) !== -1;
        };

        /**
         * @function togglePlan
         * @memberOf AccountCtrl
         *
         * @description
         *   Adds or removes all modules of a given plan.
         *
         * @param {String} plan The name of the plan.
         */
        $scope.togglePlan = function(plan) {
          var modules =$filter('filter')($scope.modules, { plan: plan });

          for (var i = 0; i < modules.length; i++) {
            var module = modules[i];
            var index = $scope.changes.indexOf(module);

            if (!$scope.isUpgraded(module) && !$scope.isDowngraded(module)) {
              if ($scope.selected[plan]) {
                // Add all plan modules
                $scope.changes.push(module);
              } else if (index !== -1) {
                // Remove all plan modules
                $scope.changes.splice(index, 1);
              }
            }
          }
        };

        $scope.getActivatedModulesForPlan = function(plan_id) {
          var modules = $filter('filter')($scope.modules, { plan: plan_id });

          var modules_activated = [];
          for (var i = modules.length - 1; i >= 0; i--) {

            if (($scope.instance.activated_modules.indexOf(modules[i].id) !== -1)) {
              modules_activated.push(modules[i]);
            }
          }

          return modules_activated;
        };

        $scope.countActivatedModulesForPlan = function(plan_id) {
          var modules = $scope.getActivatedModulesForPlan(plan_id);

          return modules.length;
        };

        // Updates the variable for changes
        $scope.$watch('instance.activated_modules', function(nv) {
          $scope.changes = [];
          for (var i = 0; i < $scope.modules.length; i++) {
            if (nv.indexOf($scope.modules[i].id) !== -1) {
              $scope.changes.push($scope.modules[i]);
            }
          }
        }, true);

        // Updates the variable with instance modules when there are changes
        $scope.$watch('changes', function(nv) {
          var modules = nv.map(function(item) {
            return item.id;
          });

          $scope.activatedModules = angular.toJson(modules);
        }, true);

        // Initializes the selected flags
        $scope.$watch('plans', function() {
          for (var i = 0; i < $scope.plans.length; i++) {
            var plan = $scope.plans[i].id;
            var total = $scope.plans[i].total;

            var modulesInPlan = $filter('filter')($scope.changes, { plan: plan });

            $scope.selected[plan] = (modulesInPlan.length === total);
          }
        }, true);
      }
    ]);
})();
