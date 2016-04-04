(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  UserGroupCtrl
     *
     * @requires $filter
     * @requires $location
     * @requires $routeParams
     * @requires $scope
     * @requires itemService
     * @requires routing
     * @requires messenger
     *
     * @description
     *   description
     */
    .controller('UserGroupCtrl', [
      '$filter', '$location', '$routeParams', '$scope', 'itemService', 'routing', 'messenger',
      function ($filter, $location, $routeParams, $scope, itemService, routing, messenger) {
        /**
         * Privileges section
         *
         * @type array
         */
        $scope.sections = [
        {
          title: 'Web',
          rows: [
            ['ADVERTISEMENT', 'WIDGET', 'MENU']
          ]
        },
        {
          title: 'Contents',
          rows: [
            ['ARTICLE', 'OPINION', 'AUTHOR', 'COMMENT'],
            ['POLL', 'STATIC', 'SPECIAL', 'LETTER'],
            ['CATEGORY', 'CONTENT']
          ]
        },
        {
          title: 'Multimedia',
          rows: [
            ['IMAGE', 'FILE', 'VIDEO', 'ALBUM'],
            ['KIOSKO', 'BOOK'],
          ]
        },
        {
          title: 'Utils',
          rows: [
            ['SEARCH', 'NEWSLETTER', 'PCLAVE', 'PAYWALL'],
            ['INSTANCE_SYNC', 'SYNC_MANAGER', 'IMPORT', 'SCHEDULE'],
          ]
        },
        {
          title: 'System',
          rows: [
            ['BACKEND', 'USER', 'GROUP', 'CACHE'],
            ['ONM']
          ]
        }
        ];

        /**
         * Selected privileges and flags
         *
         * @type Object
         */
        $scope.selected = {
          all: {},
          privileges: {},
          allSelected: {}
        };

        /**
         * Checks if all module privileges are checked.
         *
         * @param string  module Module name.
         *
         * @return boolean True, if all module privileges are checked.
         *                 Otherwise, returns false.
         */
        $scope.allSelected = function(module) {
          if (!$scope.extra || !$scope.extra.modules) {
            return;
          }

          for (var key in $scope.extra.modules[module]) {
            var id = $scope.extra.modules[module][key].id;

            if (!$scope.group.privileges ||
                $scope.group.privileges.indexOf(id) === -1
               ) {
              $scope.selected.all[module] = 0;
              return false;
            }
          }

          return true;
        };

        /**
         * Checks if a privilege is selected.
         *
         * @param  integer id The privilege id.
         *
         * @return boolean True if the privilege is selected. Otherwise, returns
         *                 false.
         */
        $scope.isSelected = function(id) {
          if (!$scope.group.privileges ||
              $scope.group.privileges.indexOf(id) == -1
             ) {
            return false;
          }

          return true;
        };

        /**
         * Creates a new user group.
         */
        $scope.save = function() {
          if ($scope.groupForm.$invalid) {
            $scope.formValidated = 1;

            messenger.post({
              message: $filter('translate')('FormErrors'),
              type:    'error'
            });

            return false;
          }

          $scope.saving = 1;

          itemService.save('manager_ws_user_group_create', $scope.group)
            .then(function (response) {
              messenger.post({
                message: response.data,
                type: response.status === 201  ? 'success' : 'error'
              });

              if (response.status === 201) {
                // Get new instance id
                var url = response.headers()['location'];
                var id  = url.substr(url.lastIndexOf('/') + 1);

                url = routing.ngGenerateShort(
                    'manager_user_group_show', { id: id });
                $location.path(url);
              }

              $scope.saving = 0;
            });
        };

        /**
         * Selects/unselects all privileges for the module.
         *
         * @param string module The module name.
         */
        $scope.selectAll = function(module) {
          if (!$scope.group.privileges) {
            $scope.group.privileges = [];
          }

          if ($scope.selected.all[module]) {
            for (var key in $scope.extra.modules[module]) {
              var id = $scope.extra.modules[module][key].id;

              if ($scope.group.privileges.indexOf(id) === -1) {
                $scope.group.privileges.push(id);
              }
            }
          } else {
            for (var key in $scope.extra.modules[module]) {
              var id = $scope.extra.modules[module][key].id;

              if ($scope.group.privileges.indexOf(id) !== -1) {
                $scope.group.privileges.splice($scope.group.privileges.indexOf(id), 1);
              }
            }
          }
        };

        /**
         * Selects/unselects all privileges
         */
        $scope.selectAllPrivileges = function() {
          if (!$scope.group.privileges) {
            $scope.group.privileges = [];
          }

          if (!$scope.selected.allSelected) {
            for (var module in $scope.extra.modules) {
              if (!$scope.selected.all[module]) {
                for (var key in $scope.extra.modules[module]) {
                  var id = $scope.extra.modules[module][key].id;

                  if ($scope.group.privileges.indexOf(id) == -1) {
                    $scope.group.privileges.push(id);
                  }
                }
                $scope.selected.allSelected = true;
              }
            }
          } else {
            $scope.selected.allSelected = false;
            $scope.group.privileges = [];
            for (var key in $scope.extra.modules[module]) {
              var id = $scope.extra.modules[module][key].id;

              if ($scope.group.privileges.indexOf(id) == -1) {
                $scope.group.privileges.splice($scope.group.privileges.indexOf(id), 1);
              }
            }
          }
        };

        /**
         * Updates an user group.
         */
        $scope.update = function() {
          if ($scope.groupForm.$invalid) {
            $scope.formValidated = 1;

            messenger.post({
              message: $filter('translate')('FormErrors'),
              type:    'error'
            });

            return false;
          }

          $scope.saving = 1;

          itemService.update('manager_ws_user_group_update', $scope.group.id,
              $scope.group).then(function (response) {
            messenger.post({
              message: response.data,
              type: response.status === 200 ? 'success' : 'error'
            });

            $scope.saving = 0;
          });
        };

        /**
         * Frees up memory before controller destroy event
         */
        $scope.$on('$destroy', function() {
          $scope.group    = null;
          $scope.sections = null;
          $scope.selected = null;
          $scope.extra    = null;
        });

        $scope.$watch('extra', function(nv) {
          if (!nv) {
            return;
          }

          // Process modules
          if ($scope.extra.modules) {
            $scope.modules = [];

            for (var module in $scope.extra.modules) {
              for (var i = 0; i < $scope.extra.modules[module].length; i++) {
                $scope.modules.push($scope.extra.modules[module][i]);
              }
            }
          }
        });

        // Initialize group
        if ($routeParams.id) {
          itemService.show('manager_ws_user_group_show', $routeParams.id).then(
            function(response) {
              $scope.group = response.data.group;
              $scope.extra = response.data.extra;
            }
          );
        } else {
          itemService.new('manager_ws_user_group_new').then(function(response) {
            $scope.group = { privileges: [] };
            $scope.extra = response.data.extra;
          });
        }
      }
  ]);
})();
