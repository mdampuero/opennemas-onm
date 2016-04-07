(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  InstanceCtrl
     *
     * @requires $filter
     * @requires $location
     * @requires $uibModal
     * @requires $scope
     * @requires itemService
     * @requires routing
     * @requires messenger
     *
     * @description
     *   Handles actions for instance edition form
     */
    .controller('InstanceCtrl', [
      '$filter', '$location', '$uibModal', '$routeParams', '$scope', 'itemService', 'routing', 'messenger',
      function ($filter, $location, $uibModal, $routeParams, $scope, itemService, routing, messenger) {
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
         *  Array that maps an UUID with an index in the array of modules.
         *
         * @type {Array}
         */
        $scope.map = {};

        /**
         * @memberOf InstanceCtrl
         *
         * @description
         *  Array of modules grouped by pack.
         *
         * @type {Array}
         */
        $scope.modulesByPack = {};

        /**
         * @memberOf InstanceCtrl
         *
         * @description
         *  Array of packs.
         *
         * @type {Array}
         */
        $scope.packs = [];

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
         * @memberOf InstanceCtrl
         *
         * @description
         *  Array of support modules
         *
         * @type {Array}
         */
        $scope.supportModules = [];

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
         * @function getClients
         * @memberOf InstanceCtrl
         *
         * @description
         *   Gets a list of clients to use in typeahead by name or email.
         *
         * @param {string} search The name or email.
         */
        $scope.getClients = function(search) {
          $scope.loading = 1;

          var data = {
            criteria: {
              name: [ { value: '%' + search + '%', operator: 'like' } ]
            },
          };

          return itemService.list('manager_ws_clients_list', data).then(
            function(response) {
              $scope.clients = response.data.results;
              $scope.loading = 0;

              return response.data.results;
            });
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
        $scope.areAllSelected = function() {
          for (var i = 0; i < $scope.selected.plan.length; i++) {
            if (!$scope.selected.plan[i]) {
              return false;
            }
          }

          return true;
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
          return _.difference($scope.modulesByPack[plan],
              $scope.instance.activated_modules).length ===0;
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
          if ($scope.instanceForm.$invalid || !$scope.instance.domains[0]) {
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
         * @function selectClient
         * @memberOf InstanceCtrl
         *
         * @description
         *   Updates the client on typeahead select.
         *
         * @param {Object} item The selected client.
         */
        $scope.selectClient = function(item) {
          $scope.instance.metas.client = item.id;
          $scope.client = item;
          $scope.search = '';
        };

        /**
         * @function selectAll
         * @memberOf InstanceCtrl
         *
         * @description
         *   Selects/unselects all modules.
         */
        $scope.toggleAll = function() {
          if ($scope.selected.all) {
            for (var i in $scope.selected.plan) {
              $scope.selected.plan[i] = true;
              $scope.togglePlan(i);
            }
          } else {
            $scope.selected.plan = {};
            $scope.instance.activated_modules = [];
          }

          //$scope.updateSupport($scope.instance.support_plan);
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
          if ($scope.selected.plan[plan]) {
            $scope.instance.activated_modules = _.uniq(_.concat(
              $scope.instance.activated_modules, $scope.modulesByPack[plan]));
          } else {
            $scope.instance.activated_modules = _.difference(
              $scope.instance.activated_modules, $scope.modulesByPack[plan]);
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
          for (var i = 0; i < data.template.modules.length; i++) {
            var module = data.template.modules[i];

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

        $scope.$watch('instance.support_plan', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if ($scope.instance.activated_modules.indexOf(ov) !== -1) {
            $scope.instance.activated_modules
              .splice($scope.instance.activated_modules.indexOf(ov), 1);
          }

          if ($scope.instance.activated_modules.indexOf(nv) === -1) {
            $scope.instance.activated_modules.push(nv);
          }
        }, true);

        $scope.$watch('instance.activated_modules', function() {
          var all = true;

          // Initializes the selected flags
          for (var i = 0; i < $scope.packs.length; i++) {
            var pack = $scope.packs[i];

            $scope.selected.plan[pack] = _.difference($scope.modulesByPack[pack],
                $scope.instance.activated_modules) == 0;

            all = all && $scope.selected.plan[pack];
          }
          $scope.selected.all = all;
        },true);

        // Remove client when instance meta is deleted
        $scope.$watch('instance.metas.client', function(nv) {
          if (!nv) {
            $scope.client = null;
          }
        });

        $scope.initModules = function() {
          var modules = [];
          var modulesInAPack = [];
          for (var i = 0; i < $scope.template.modules.length; i++) {
            var module = $scope.template.modules[i];

            // Generate a map of modules for easy-access
            $scope.map[module.uuid] = i;

            modules.push(module.uuid);

            // Ignore grouping for PACKS and SUPPORT
            if (/SUPPORT_/.test(module.uuid)) {
              $scope.supportModules.push(module.uuid);
            }

            // Ignore grouping for PACKS and SUPPORT
            if (module.metas.modules_included || /SUPPORT_/.test(module.uuid)) {
              modulesInAPack.push(module.uuid);
            }

            // If it is a pack
            if (module.metas && module.metas.modules_included) {
              $scope.packs.push(module.uuid);

              // Get modules that this pack adds to the previous pack
              $scope.modulesByPack[module.uuid] = _.difference(
                  module.metas.modules_included, modulesInAPack);

              // Other packs, get all modules in the pack
              if (module.uuid !== 'BASIC_PACK' &&
                  module.uuid !== 'PROFESSIONAL_PACK' &&
                  module.uuid !== 'ADVANCED_PACK' &&
                  module.uuid !== 'EXPERT_PACK') {
                $scope.modulesByPack[module.uuid] = module.metas.modules_included;
              }

              modulesInAPack = modulesInAPack.concat(module.metas.modules_included);
            }
          }

          // Initialize flags and group modules for OTHER_PACK
          $scope.packs.push('OTHER_PACK');
          $scope.modulesByPack.OTHER_PACK = _.difference(modules, modulesInAPack);
          $scope.selected.plan.OTHER_PACK = _.difference($scope.modulesByPack.OTHER_PACK,
              $scope.instance.activated_modules) == 0;
        }

        $scope.$on('$destroy', function() {
          $scope.instance = null;
          $scope.changed_modules = null;
          $scope.template = null;
          $scope.selected = null;
        });

        if ($routeParams.id) {
          itemService.show('manager_ws_instance_show', $routeParams.id).then(
            function(response) {
              $scope.template = response.data.template;
              $scope.instance = response.data.instance;

              if (!$scope.instance.metas) {
                $scope.instance.metas = {};
              }

              $scope.initModules();
            }
          );
        } else {
          itemService.new('manager_ws_instance_new').then(
            function(response) {
              $scope.template = response.data.template;

              // Select Base plan as default
              for (var i = 0; i < response.data.template.modules.length; i++) {
                if (response.data.template.modules[i].plan == 'Base') {
                  $scope.instance.activated_modules.push(
                      response.data.template.modules[i].id);
                }
              }

              $scope.initModules();
            }
          );
        }
      }
    ]);
})();
