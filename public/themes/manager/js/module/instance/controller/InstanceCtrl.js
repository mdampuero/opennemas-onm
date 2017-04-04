(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  InstanceCtrl
     *
     * @requires $filter
     * @requires $location
     * @required $routeParams
     * @requires $scope
     * @requires $uibModal
     * @requires http
     * @requires messenger
     *
     * @description
     *   Handles actions for instance edition form
     */
    .controller('InstanceCtrl', [
      '$filter', '$location', '$routeParams', '$scope', '$uibModal', 'http', 'messenger', 'oqlEncoder',
      function ($filter, $location, $routeParams, $scope, $uibModal, http, messenger, oqlEncoder) {
        /**
         * @memberOf InstanceCtrl
         *
         * @description
         *  Criteria to search clients.
         *
         * @type {Object}
         */
        $scope.criteria = { epp: 10 };

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
          settings: {
            TEMPLATE_USER: 'base'
          },
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
         *   The instance settings.
         *
         * @type {Object}
         */
        $scope.settings = {
          site_language: 'es_ES',
          pass_level:    -1,
          max_mailing:   0,
          time_zone:     '335'
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

          oqlEncoder.configure({
            placeholder: {
              name: 'first_name ~ "[value]" or last_name ~ "[value]" or' +
                ' address ~ "[value]" or city ~ "[value]" or state ~ "[value]"'
            }
          });

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: 'manager_ws_clients_list',
            params: { oql:  oql }
          };

          return http.get(route).then(
            function(response) {
              $scope.clients = response.data.results;
              $scope.loading = 0;

              return response.data.results;
            }, function() {
              $scope.loading = 0;
            });
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
          for (var i = 0; i < $scope.packs.length; i++) {
            if (!$scope.selected.plan[$scope.packs[i]]) {
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

          if ($scope.instance.main_domain - 1 > index) {
            $scope.instance.main_domain--;
          }

          if ($scope.instance.main_domain > $scope.instance.domains.length) {
            $scope.instance.main_domain = $scope.instance.domains.length;
          }
        };

        /**
         * @function save
         * @memberOf InstanceCtrl
         *
         * @description
         *   Creates a new instance.
         */
        $scope.save = function() {
          $scope.saving = 1;

          http.post('manager_ws_instance_save', { instance: $scope.instance,
            settings: $scope.settings }).then(function (response) {
              messenger.post(response.data);

              if (response.status === 201) {
                // Add instance to owned instances
                if (!$scope.security.hasPermission('MASTER') &&
                    $scope.security.hasPermission('PARTNER')) {
                  $scope.refreshSecurity();
                }

                var url = response.headers().location.replace('/managerws', '');
                $location.path(url);
              }
            }, function(response) {
              messenger.post(response.data);
              $scope.saving = 0;
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
          $scope.instance.client = item.id;
          $scope.client = item;
          $scope.criteria = { name: '' };
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
            for (var i = 0; i < $scope.packs.length; i++) {
              if ($scope.security.canEnable($scope.packs[i])) {
                $scope.selected.plan[$scope.packs[i]] = true;
                $scope.togglePlan($scope.packs[i]);
              }
            }
          } else {
            $scope.selected.plan = {};
            $scope.instance.activated_modules = [];
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
          $scope.saving = 1;

          var data  = { instance: $scope.instance, settings: $scope.settings };
          var route = {
            name:   'manager_ws_instance_update',
            params: { id: $scope.instance.id }
          };

          http.put(route, data).then(function (response) {
            messenger.post(response.data);
            $scope.saving = 0;
          }, function(response) {
            messenger.post(response.data);
            $scope.saving = 0;
          });
        };

        $scope.$watch('[packs, instance.activated_modules]', function() {
          var all = true;

          // Initializes the selected flags
          for (var i = 0; i < $scope.packs.length; i++) {
            var pack = $scope.packs[i];

            $scope.selected.plan[pack] = _.difference($scope.modulesByPack[pack],
                $scope.instance.activated_modules).length === 0;

            all = all && $scope.selected.plan[pack];
          }

          $scope.selected.all = all;
        }, true);

        // Remove client when instance meta is deleted
        $scope.$watch('instance.client', function(nv) {
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

            // Ignore grouping for PACKS
            if (module.modules_included) {
              modulesInAPack.push(module.uuid);
            }

            // If it is a pack
            if (module.modules_included) {
              $scope.packs.push(module.uuid);

              // Get modules that this pack adds to the previous pack
              $scope.modulesByPack[module.uuid] = _.difference(
                  module.modules_included, modulesInAPack);

              // Other packs, get all modules in the pack
              if (module.uuid !== 'BASIC_PACK' &&
                  module.uuid !== 'PROFESSIONAL_PACK' &&
                  module.uuid !== 'ADVANCED_PACK' &&
                  module.uuid !== 'EXPERT_PACK') {
                $scope.modulesByPack[module.uuid] = module.modules_included;
              }

              modulesInAPack = modulesInAPack.concat(module.modules_included);
            }
          }

          // Initialize flags and group modules for OTHER_PACK
          $scope.packs.push('OTHER_PACK');
          $scope.modulesByPack.OTHER_PACK = _.difference(modules, modulesInAPack);
          $scope.selected.plan.OTHER_PACK = _.difference($scope.modulesByPack.OTHER_PACK,
              $scope.instance.activated_modules) === 0;
        };

        $scope.$on('$destroy', function() {
          $scope.instance = null;
          $scope.template = null;
          $scope.selected = null;
        });

        var route = 'manager_ws_instance_new';

        if ($routeParams.id) {
          route = {
            name:   'manager_ws_instance_show',
            params: { id: $routeParams.id }
          };
        }

        http.get(route).then(function(response) {
          $scope.template = response.data.template;

          if ($scope.template.client) {
            $scope.client = $scope.template.client;
          }

          if (response.data.instance) {
            $scope.instance = angular.merge($scope.instance, response.data.instance);
            $scope.settings = angular.merge($scope.settings, response.data.settings);
          }

          // Select Base plan as default
          for (var i = 0; i < response.data.template.modules.length; i++) {
            if (response.data.template.modules[i].plan === 'Base') {
              $scope.instance.activated_modules.push(
                  response.data.template.modules[i].id);
            }
          }

          $scope.initModules();
        });
      }
    ]);
})();
