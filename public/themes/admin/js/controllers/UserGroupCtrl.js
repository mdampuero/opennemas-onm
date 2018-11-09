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
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

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
         * @memberOf SubscriptionCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          create:   'api_v1_backend_user_group_create',
          redirect: 'backend_user_group_show',
          save:     'api_v1_backend_user_group_save',
          show:     'api_v1_backend_user_group_show',
          update:   'api_v1_backend_user_group_update'
        };

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
            rows: [ [ 'SECURITY', 'INTERNAL', 'ONM' ] ]
          },
          {
            title: 'Web',
            rows: [
              [ 'ADS_MANAGER', 'WIDGET_MANAGER', 'MENU_MANAGER', 'AUTHOR' ],
              [ 'es.openhost.module.tags' ]
            ]
          },
          {
            title: 'Contents',
            rows: [
              [ 'CATEGORY_MANAGER', 'CONTENT' ],
              [ 'ARTICLE_MANAGER', 'OPINION_MANAGER', 'COMMENT_MANAGER' ],
              [ 'POLL_MANAGER', 'STATIC_PAGES_MANAGER', 'SPECIAL_MANAGER', 'LETTER_MANAGER' ],
              [ 'es.openhost.module.events' ],
            ]
          },
          {
            title: 'Multimedia',
            rows: [
              [ 'IMAGE_MANAGER', 'FILE_MANAGER', 'VIDEO_MANAGER', 'ALBUM_MANAGER' ],
              [ 'KIOSKO_MANAGER', 'BOOK_MANAGER' ],
            ]
          },
          {
            title: 'Utils',
            rows: [
              [ 'ADVANCED_SEARCH', 'NEWSLETTER_MANAGER', 'KEYWORD_MANAGER', 'PAYWALL' ],
              [ 'SYNC_MANAGER', 'NEWS_AGENCY_IMPORTER' ],
            ]
          },
          {
            title: 'System',
            rows: [
              [ 'USER_MANAGER', 'USER_GROUP_MANAGER', 'CACHE_MANAGER' ],
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
         * @function getItemId
         * @memberOf UserGroupCtrl
         *
         * @description
         *   Returns the item id.
         *
         * @return {Integer} The item id.
         */
        $scope.getItemId = function() {
          return $scope.item.pk_user_group;
        };

        /**
         * @function itemHasId
         * @memberOf UserGroupCtrl
         *
         * @description
         *   Checks if the current item has an id.
         *
         * @return {Boolean} description
         */
        $scope.itemHasId = function() {
          return $scope.item.pk_user_group &&
            $scope.item.pk_user_group !== null;
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
              return e.pk_privilege;
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
              $scope.privileges.push(module[i].pk_privilege);
              $scope.modules[name].push(module[i].pk_privilege);

              if ($scope.item.privileges.indexOf(module[i].pk_privilege) === -1) {
                $scope.selected.all[name] = false;
              }
            }
          }
        }, true);
      }
    ]);
})();
