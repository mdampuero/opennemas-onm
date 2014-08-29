/**
 * Handles all actions in user groups listing.
 *
 * @param  Object $scope      The current scope.
 * @param  Object itemService The item service.
 * @param  Object data        The input data.
 *
 * @return Object The command controller.
 */
angular.module('ManagerApp.controllers').controller('UserGroupCtrl',
    function ($location, $scope, itemService, fosJsRouting, messenger, data) {
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

                if (!$scope.group.privileges
                        || $scope.group.privileges.indexOf(id) == -1) {
                    $scope.selected.all[module] = 0;
                    return false
                }
            }

            return true;
        }

        /**
         * Checks if a privilege is selected.
         *
         * @param  integer id The privilege id.
         *
         * @return boolean True if the privilege is selected. Otherwise, returns
         *                 false.
         */
        $scope.isSelected = function(id) {
            if (!$scope.group.privileges
                    || $scope.group.privileges.indexOf(id) == -1) {
                return false
            }

            return true
        }

        /**
         * Creates a new user group.
         */
        $scope.save = function() {
            $scope.saving = 1;

            itemService.save('manager_ws_user_group_create', $scope.group)
                .then(function (response) {
                    if (response.data.success) {
                        $location.path(fosJsRouting.ngGenerateShort('/manager',
                            'manager_user_group_show',
                            { id: response.data.message.id }));
                    }

                    messenger.post({
                        message: response.data.message.text,
                        type:    response.data.message.type
                    });

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

                    if ($scope.group.privileges.indexOf(id) == -1) {
                        $scope.group.privileges.push(id);
                    }
                }
            } else {
                for (var key in $scope.template.modules[module]) {
                    var id = $scope.template.modules[module][key].id;

                    if ($scope.group.privileges.indexOf(id) != -1) {
                        $scope.group.privileges.splice($scope.group.privileges.indexOf(id), 1);
                    }
                };
            }
        };

        $scope.toggleAllPrivileges = function() {
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
            };
        }


        /**
         * Updates an user group.
         */
        $scope.update = function() {
            $scope.saving = 1;

            itemService.update('manager_ws_user_group_update', $scope.group.id,
                $scope.group).then(function (response) {
                    messenger.post({
                        message: response.data.message.text,
                        type:    response.data.message.type
                    });

                    $scope.saving = 0;
                });
        };

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
                };
            };
        }
    }
);
