(function () {
  'use strict';

  angular.module('BackendApp.controllers')
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
       '$location', '$scope', 'http', 'routing', 'messenger',
      function ($location, $scope, http, routing, messenger) {
        /**
         * @memberOf UserGroupCtrl
         *
         * @description
         *  List of privileges grouped by extension.
         *
         * @type {Array}
         */
        $scope.modules = [];

        /**
         * @memberOf UserGroupCtrl
         *
         * @description
         *  List of privileges.
         *
         * @type {type}
         */
        $scope.privileges = [];

        /**
         * @memberOf UserGroupCtrl
         *
         * Privileges section
         *
         * @type array
         */
        $scope.sections = [
        {
          title: 'Opennemas',
          rows: [
            ['SECURITY', 'INTERNAL']
          ]
        },
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
         * @memberOf UserGroupCtrl
         *
         * Selected privileges and flags
         *
         * @type Object
         */
        $scope.selected = { all: {}, privileges: {}, allSelected: false };

        /**
         * @memberOf UserGroupCtrl
         *
         * List of available groups.
         *
         * @type Object
         */
        $scope.user_group = { privileges: [] };

        /**
         * @function areAllSelected
         * @memberOf UserGroupCtrl
         *
         * @description
         *   Checks if all privileges are selected.
         *
         * @return {Boolean} True if all privileges are selected. Otherwise,
         *                   returns false.
         */
        $scope.areAllSelected = function() {
          if (!$scope.extra || !$scope.extra.modules) {
            return true;
          }

          var diff = _.difference($scope.privileges, $scope.user_group.privileges);
          var selected = diff.length === 0;

          if (selected !== $scope.selected.allSelected) {
            $scope.selected.allSelected = selected;
          }

          return selected;

        };

        /**
         * @function isModuleSelected
         * @memberOf UserGroupCtrl
         *
         * @description
         *   Checks if all module privileges are checked.
         *
         * @param {String} module The module name.
         *
         * @return {Boolean} True if the module is selected. Otherwise, returns
         *                   false.
         */
        $scope.isModuleSelected = function(module) {
          if (!$scope.extra || !$scope.extra.modules || !$scope.modules ||
              !$scope.modules[module]) {
            return;
          }

          var diff = _.difference($scope.modules[module], $scope.user_group.privileges);
          var selected = diff.length === 0;

          if (selected !== $scope.selected.all[module]) {
            $scope.selected.all[module] = selected;
          }

          return selected;
        };

        /**
         * @function save
         * @memberOf UserGroupCtrl
         *
         * @description
         *   Saves a new user group.
         */
        $scope.save = function() {
          $scope.saving = 1;

          http.post('manager_ws_user_group_save', $scope.user_group)
            .then(function (response) {
              messenger.post(response.data);

              if (response.status === 201) {
                var url = response.headers().location.replace('/managerws', '');
                $location.path(url);
              }
            }, function(response) {
              messenger.post(response.data);
              $scope.saving = 0;
            });
        };

        $scope.selectAll = function() {
          if (!$scope.selected.allSelected) {
            $scope.selected.all = {};
            $scope.user_group.privileges = [];
            return;
          }

          var ids = [];
          for (var i in $scope.extra.modules) {
            $scope.selected.all[$scope.extra.modules[i].name] = true;
            var privileges = $scope.extra.modules[i].map(function(e) {
              return e.id;
            });

            ids = _.union(ids, privileges);
          }

          $scope.user_group.privileges = ids;
        };

        /**
         * @function selectedModule
         * @memberOf UserGroupCtrl
         *
         * @description
         *   Selects/unselects all module privileges.
         *
         * @param {String} name The module name.
         */
        $scope.selectModule = function(name) {
          if (!$scope.user_group.privileges) {
            $scope.user_group.privileges = [];
          }

           // Add module privileges
          if ($scope.selected.all[name]) {
            $scope.user_group.privileges =
              _.union($scope.user_group.privileges, $scope.modules[name]);
            return;
          }

          // Remove module privileges
          $scope.user_group.privileges =
            _.difference($scope.user_group.privileges, $scope.modules[name]);
        };

        /**
         * @function update
         * @memberOf UserGroupCtrl
         *
         * @description
         *   Updates the user group.
         */
        $scope.update = function() {
          $scope.saving = 1;

          var route = {
            name: 'manager_ws_user_group_update',
            params: { id: $scope.user_group.pk_user_group }
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

        $scope.$watch('extra.modules', function () {
          // Initialize selected all flags
          for (var name in $scope.extra.modules) {
            if (!$scope.modules[name]) {
              $scope.modules[name] = [];
            }

            var module = $scope.extra.modules[name];
            $scope.selected.all[name] = true;

            for (var i = 0; i < module.length; i++) {
              $scope.privileges.push(module[i].id);
              $scope.modules[name].push(module[i].id);

              if ($scope.user_group.privileges.indexOf(module[i].pk_privilege) === -1) {
                $scope.selected.all[name] = false;
              }
            }
          }
        }, true);

        // Update privilege value in form when user.privileges change
        $scope.$watch('user_group.privileges', function(nv) {
          $scope.permissions = '';

          if (nv) {
            $scope.permissions = nv.join(',');
          }
        }, true);
      }
  ]);
})();
