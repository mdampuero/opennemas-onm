'use strict';

/**
 * Handles all actions in user groups listing.
 *
 * @param  Object $scope      The current scope.
 * @param  Object itemService The item service.
 * @param  Object data        The input data.
 *
 * @return Object The command controller.
 */
angular.module('ManagerApp.controllers').controller('UserGroupCtrl', [
    '$filter', '$location', '$scope', 'itemService', 'routing', 'messenger', 'data',
    function ($filter, $location, $scope, itemService, routing, messenger, data) {
        /**
         * List of available groups.
         *
         * @type Object
         */
        $scope.group = {
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
         * The template parameters.
         *
         * @type Object
         */
        $scope.template = data.template;

        /**
         * Checks if all module privileges are checked.
         *
         * @param string  module Module name.
         *
         * @return boolean True, if all module privileges are checked.
         *                 Otherwise, returns false.
         */
        $scope.allSelected = function(module) {
            for (var key in $scope.template.modules[module]) {
                    var id = $scope.template.modules[module][key].id;

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
                for (var key in $scope.template.modules[module]) {
                    var id = $scope.template.modules[module][key].id;

                    if ($scope.group.privileges.indexOf(id) === -1) {
                        $scope.group.privileges.push(id);
                    }
                }
            } else {
                for (var key in $scope.template.modules[module]) {
                    var id = $scope.template.modules[module][key].id;

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
                for (var module in $scope.template.modules) {
                    if (!$scope.selected.all[module]) {
                        for (var key in $scope.template.modules[module]) {
                            var id = $scope.template.modules[module][key].id;

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
                for (var key in $scope.template.modules[module]) {
                    var id = $scope.template.modules[module][key].id;

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
            $scope.template = null;
        });

        // Initialize group
        if (data.group) {
            $scope.group = data.group;
        }

        // Process modules
        if ($scope.template.modules) {
            $scope.modules = [];

            for (var module in $scope.template.modules) {
                for (var i = 0; i < $scope.template.modules[module].length; i++) {
                    $scope.modules.push($scope.template.modules[module][i]);
                }
            }
        }
    }
]);
