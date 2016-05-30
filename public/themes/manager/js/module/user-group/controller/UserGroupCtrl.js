(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  UserGroupCtrl
     *
     * @requires $location
     * @requires $routeParams
     * @requires $scope
     * @requires http
     * @requires routing
     * @requires messenger
     *
     * @description
     *   Handles all actions in user groups listing.
     */
    .controller('UserGroupCtrl', [
       '$location', '$routeParams', '$scope', 'http', 'routing', 'messenger',
      function ($location, $routeParams, $scope, http, routing, messenger) {
        /**
         * List of available groups.
         *
         * @type Object
         */
        $scope.user_group = {
          privileges: []
        };

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
        $scope.selected = { all: {}, privileges: {}, allSelected: {} };

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

            if (!$scope.user_group.privileges ||
                $scope.user_group.privileges.indexOf(id) === -1
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
          if (!$scope.user_group.privileges ||
              $scope.user_group.privileges.indexOf(id) == -1
             ) {
            return false;
          }

          return true;
        };

        /**
         * Saves a new user group.
         */
        $scope.save = function() {
          $scope.saving = 1;

          http.post('manager_ws_user_group_save', $scope.user_group)
            .then(function (response) {
              messenger.post(response.data);

              if (response.status === 201) {
                var url = response.headers().location.replace('/manager', '');
                $location.path(url);
              }
            }, function(response) {
              messenger.post(response.data);
              $scope.saving = 0;
            });
        };

        /**
         * Selects/unselects all privileges for the module.
         *
         * @param string module The module name.
         */
        $scope.selectAll = function(module) {
          if (!$scope.user_group.privileges) {
            $scope.user_group.privileges = [];
          }

          if ($scope.selected.all[module]) {
            for (var key in $scope.extra.modules[module]) {
              var id = $scope.extra.modules[module][key].id;

              if ($scope.user_group.privileges.indexOf(id) === -1) {
                $scope.user_group.privileges.push(id);
              }
            }
          } else {
            for (var key in $scope.extra.modules[module]) {
              var id = $scope.extra.modules[module][key].id;

              if ($scope.user_group.privileges.indexOf(id) !== -1) {
                $scope.user_group.privileges.splice($scope.user_group.privileges.indexOf(id), 1);
              }
            }
          }
        };

        /**
         * Selects/unselects all privileges
         */
        $scope.selectAllPrivileges = function() {
          if (!$scope.user_group.privileges) {
            $scope.user_group.privileges = [];
          }

          if (!$scope.selected.allSelected) {
            for (var module in $scope.extra.modules) {
              if (!$scope.selected.all[module]) {
                for (var key in $scope.extra.modules[module]) {
                  var id = $scope.extra.modules[module][key].id;

                  if ($scope.user_group.privileges.indexOf(id) == -1) {
                    $scope.user_group.privileges.push(id);
                  }
                }
                $scope.selected.allSelected = true;
              }
            }
          } else {
            $scope.selected.allSelected = false;
            $scope.user_group.privileges = [];
            for (var key in $scope.extra.modules[module]) {
              var id = $scope.extra.modules[module][key].id;

              if ($scope.user_group.privileges.indexOf(id) == -1) {
                $scope.user_group.privileges.splice($scope.user_group.privileges.indexOf(id), 1);
              }
            }
          }
        };

        /**
         * Updates an user group.
         */
        $scope.update = function() {
          $scope.saving = 1;

          var route = {
            name: 'manager_ws_user_group_update',
            params: { id: $scope.user_group.id }
          };

          http.put(route, $scope.user_group)
            .then(function (response) {
              messenger.post(response.data);
              $scope.saving = 0;
            }, function(response) {
              messenger.post(response.data);
              $scope.saving = 0;
            });
        };

        // Frees up memory on destroy
        $scope.$on('$destroy', function() {
          $scope.user_group = null;
          $scope.sections   = null;
          $scope.selected   = null;
          $scope.extra      = null;
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

        var route = 'manager_ws_user_group_new';

        if ($routeParams.id) {
          route = { name: 'manager_ws_user_group_show',
            params: { id: $routeParams.id } };
        }

        http.get(route).then(function(response) {
          $scope.extra = response.data.extra;

          if (response.data.user_group) {
            $scope.user_group = angular.merge(response.data.user_group);

            // Initialize selected all flags
            for (var name in $scope.extra.modules) {
              var module = $scope.extra.modules[name];
              $scope.selected.all[name] = true;

              for (var i = 0; i < module.length; i++) {
                if ($scope.user_group.privileges.indexOf(module[i].pk_privilege) === -1) {
                  $scope.selected.all[name] = false;
                }
              }
            }
          }
        });
      }
  ]);
})();
