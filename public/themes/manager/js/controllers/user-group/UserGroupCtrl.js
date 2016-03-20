(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  UserGroupCtrl
     *
     * @requires $location
     * @requires $scope
     * @requires http
     * @requires routing
     * @requires messenger
     * @requires data
     *
     * @description
     *   Handles all actions in user groups listing.
     */
    .controller('UserGroupCtrl', [
      '$location', '$scope', 'http', 'routing', 'messenger', 'data',
      function ($location, $scope, http, routing, messenger, data) {
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
        $scope.selected = {
          all: {},
          privileges: {},
          allSelected: {}
        };

        /**
         * The extra parameters.
         *
         * @type Object
         */
        $scope.extra = data.extra;

        /**
         * Checks if all module privileges are checked.
         *
         * @param string  module Module name.
         *
         * @return boolean True, if all module privileges are checked.
         *                 Otherwise, returns false.
         */
        $scope.allSelected = function(module) {
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
              $scope.user_group.privileges.indexOf(id) === -1
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
                // Get new instance id
                var url = response.headers().location;
                var id  = url.substr(url.lastIndexOf('/') + 1);

                url = routing.ngGenerateShort(
                    'manager_user_group_show', { id: id });
                $location.path(url);
              }

              $scope.saving = 0;
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

                  if ($scope.user_group.privileges.indexOf(id) === -1) {
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

              if ($scope.user_group.privileges.indexOf(id) === -1) {
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

        /**
         * Frees up memory before controller destroy event
         */
        $scope.$on('$destroy', function() {
          $scope.user_group = null;
          $scope.sections   = null;
          $scope.selected   = null;
          $scope.extra      = null;
        });

        // Initialize user group
        if (data.user_group) {
          $scope.user_group = data.user_group;
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
      }
    ]);
})();
