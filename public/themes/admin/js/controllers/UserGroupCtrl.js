(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  UserGroupCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $window
     * @requires cleaner
     * @requires http
     * @requires messenger
     * @requires routing
     *
     * @description
     *   Handles all actions in user groups listing.
     */
    .controller('UserGroupCtrl', [
      '$controller', '$scope', '$window', 'cleaner', 'http', 'messenger', 'routing',
      function($controller, $scope, $window, cleaner, http, messenger, routing) {
        // Initialize the super class and extend it
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf UserGroupCtrl
         *
         * @description
         *   The user group object..
         *
         * @type {Object}
         */
        $scope.item = {
          privileges: [],
          type: 0
        };

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
         * @type {Array}
         */
        $scope.privileges = [];

        /**
         * @memberOf UserGroupCtrl
         *
         * Privileges sections.
         *
         * @type {Array}
         */
        $scope.sections = [
          {
            title: 'Opennemas',
            rows: [ [ 'SECURITY', 'INTERNAL' ] ]
          },
          {
            title: 'Web',
            rows: [ [ 'ADVERTISEMENT', 'WIDGET', 'MENU' ] ]
          },
          {
            title: 'Contents',
            rows: [
              [ 'ARTICLE', 'OPINION', 'AUTHOR', 'COMMENT' ],
              [ 'POLL', 'STATIC', 'SPECIAL', 'LETTER' ],
              [ 'CATEGORY', 'CONTENT' ]
            ]
          },
          {
            title: 'Multimedia',
            rows: [
              [ 'IMAGE', 'FILE', 'VIDEO', 'ALBUM' ],
              [ 'KIOSKO', 'BOOK' ],
            ]
          },
          {
            title: 'Utils',
            rows: [
              [ 'SEARCH', 'NEWSLETTER', 'PCLAVE', 'PAYWALL' ],
              [ 'INSTANCE_SYNC', 'SYNC_MANAGER', 'IMPORT', 'SCHEDULE' ],
            ]
          },
          {
            title: 'System',
            rows: [
              [ 'BACKEND', 'USER', 'GROUP', 'CACHE' ],
              [ 'ONM' ]
            ]
          }
        ];

        /**
         * @memberOf UserGroupCtrl
         *
         * Selected privileges and flags.
         *
         * @type {Object}
         */
        $scope.selected = {
          all: {},
          allSelected: false,
          privileges: {}
        };

        /**
         * @function areAllSelected
         * @memberOf UserGroupCtrl
         *
         * @description
         *   Checks if all permissions are selected.
         *
         * @return {Boolean} True if all permissions are selected. Otherwise,
         *                   returns false.
         */
        $scope.areAllSelected = function() {
          if (!$scope.data || !$scope.data.extra ||
              !$scope.data.extra.modules) {
            return false;
          }

          var diff = _.difference($scope.privileges,
            $scope.item.privileges);
          var selected = diff.length === 0;

          if (selected !== $scope.selected.allSelected) {
            $scope.selected.allSelected = selected;
          }

          return selected;
        };

        /**
         * @function getItem
         * @memberOf UserGroupCtrl
         *
         * @description
         *   Gets the subscription to show.
         *
         * @param {Integer} id The subscription id.
         */
        $scope.getItem = function(id) {
          $scope.flags.loading = 1;

          var route = !id ? 'api_v1_backend_user_group_create' :
            { name: 'api_v1_backend_user_group_show', params: { id: id } };

          http.get(route).then(function(response) {
            $scope.data = response.data;

            if ($scope.data.user_group) {
              $scope.item = $scope.data.user_group;
            }

            $scope.disableFlags();
          }, function() {
            $scope.item = null;

            $scope.disableFlags();
          });
        };

        /**
         * @function init
         * @memberOf UserGroupCtrl
         *
         * @description
         *   Initializes the user group.
         *
         * @param {Integer} id The user group id when editing.
         */
        $scope.init = function(id) {
          $scope.getItem(id);
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
          if (!$scope.data || !$scope.data.extra || !$scope.data.extra.modules ||
              !$scope.modules || !$scope.modules[module]) {
            return false;
          }

          var diff = _.difference($scope.modules[module],
            $scope.item.privileges);
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
          if ($scope.userGroupForm.$invalid) {
            return;
          }

          $scope.userGroupForm.$setPristine(true);
          $scope.flags.saving = 1;

          var data = cleaner.clean($scope.item);

          /**
           * Callback executed when subscription is saved/updated successfully.
           *
           * @param {Object} response The response object.
           */
          var successCb = function(response) {
            $scope.disableFlags();

            if (response.status === 201) {
              var id = response.headers().location
                .substring(response.headers().location.lastIndexOf('/') + 1);

              $window.location.href =
                routing.generate('backend_user_group_show', { id: id });
            }

            messenger.post(response.data);
          };

          if (!$scope.item.pk_user_group) {
            var route = { name: 'api_v1_backend_user_group_create' };

            http.post(route, data).then(successCb, $scope.errorCb);
            return;
          }

          http.put({
            name: 'api_v1_backend_user_group_update',
            params: { id: $scope.item.pk_user_group }
          }, data).then(successCb, $scope.errorCb);
        };

        /**
         * @function selectAll
         * @memberOf UserGroupCtrl
         *
         * @description
         *   Selects all permissions.
         */
        $scope.selectAll = function() {
          if (!$scope.selected.allSelected) {
            $scope.selected.all = {};
            $scope.item.privileges = [];
            return;
          }

          var ids = [];

          for (var i in $scope.data.extra.modules) {
            $scope.selected.all[$scope.data.extra.modules[i].name] = true;
            var privileges = $scope.data.extra.modules[i].map(function(e) {
              return e.id;
            });

            ids = _.union(ids, privileges);
          }

          $scope.item.privileges = ids;
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
          if (!$scope.item.privileges) {
            $scope.item.privileges = [];
          }

          // Add module privileges
          if ($scope.selected.all[name]) {
            $scope.item.privileges = _.union(
              $scope.item.privileges, $scope.modules[name]);
            return;
          }

          // Remove module privileges
          $scope.item.privileges = _.difference(
            $scope.item.privileges, $scope.modules[name]);
        };

        // Initialize and sort privileges and flags
        $scope.$watch('data.extra.modules', function(nv) {
          if (!nv) {
            return;
          }

          for (var name in nv) {
            if (!$scope.modules[name]) {
              $scope.modules[name] = [];
            }

            var module = nv[name];

            $scope.selected.all[name] = true;

            for (var i = 0; i < module.length; i++) {
              $scope.privileges.push(module[i].id);
              $scope.modules[name].push(module[i].id);

              if ($scope.item.privileges.indexOf(module[i].id) === -1) {
                $scope.selected.all[name] = false;
              }
            }
          }
        }, true);
      }
    ]);
})();
