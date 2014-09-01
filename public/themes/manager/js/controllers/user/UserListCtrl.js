/**
 * Handles all actions in users listing.
 *
 * @param  Object $modal       The modal service.
 * @param  Object $scope       The current scope.
 * @param  Object $timeout     The timeout service.
 * @param  Object itemService  The item service.
 * @param  Object fosJsRouting The fosJsRouting service.
 * @param  Object messenger    The messenger service.
 * @param  Object data         The input data.
 *
 * @return Object The user list controller.
 */
angular.module('ManagerApp.controllers').controller('UserListCtrl',
    function ($modal, $scope, $timeout, itemService, fosJsRouting, messenger, data) {
        /**
         * The criteria to search.
         *
         * @type Object
         */
        $scope.criteria = {
            name: [ { value: '', operator: 'like' } ],
            fk_user_group: [ { value: '-1', operator: 'regexp' } ]
        };

        /**
         * The number of elements per page
         *
         * @type integer
         */
        $scope.epp  = 25;

        /**
         * The list of selected elements.
         *
         * @type array
         */
        $scope.selected = {
            all: false,
            users: []
        };

        /**
         * The listing order.
         *
         * @type Object
         */
        $scope.orderBy = {
            'name': 'asc'
        }

        /**
         * List of template parameters
         *
         * @type Object
         */
        $scope.template = data.template;

        /**
         * The number of total items.
         *
         * @type integer
         */
        $scope.total = data.total;

        /**
         * List of available users.
         *
         * @type Object
         */
        $scope.users = data.results;

        /**
         * Variable to store the current search.
         */
        var search;

        /**
         * Confirm delete action.
         */
        $scope.delete = function(user) {
            var modal =  $modal.open({
                templateUrl: '/managerws/template/common:modal_confirm_delete.tpl',
                controller:  'UserModalCtrl',
                resolve: {
                    selected: function() {
                        return user;
                    }
                }
            });

            modal.result.then(function(data) {
                if (data) {
                    list();
                }
            });
        };

        /**
         * Confirm delete action.
         */
        $scope.deleteSelected = function() {
            var modal =  $modal.open({
                templateUrl: '/managerws/template/common:modal_confirm_delete.tpl',
                controller:  'UserModalCtrl',
                resolve: {
                    selected: function() {
                        return $scope.selected.users;
                    }
                }
            });

            modal.result.then(function(data) {
                if (data) {
                    list();
                }
            });
        };

        /**
         * Selects/unselects all users.
         */
        $scope.selectAll = function() {
            if ($scope.selected.all) {
                $scope.selected.users = $scope.users.map(function(user) {
                    return user.id;
                });
            } else {
                $scope.selected.users = [];
            }
        };

        /**
         * Enables/disables an user.
         *
         * @param boolean enabled Instance activated value.
         */
        $scope.setEnabled = function(user, enabled) {
            user.loading = 1;

            itemService.setEnabled('manager_ws_user_set_enabled',
                user.id, enabled).then(function (response) {
                    user.loading = 0;

                    if (response.data.success) {
                        user.activated = enabled;
                    }

                    messenger.post({
                        message: response.data.message.text,
                        type:    response.data.message.type
                    });
                });
        }

        /**
         * Enables/disables the selected users.
         *
         * @param integer enabled The activated value.
         */
        $scope.setEnabledSelected = function(enabled) {
            for (var i = 0; i < $scope.users.length; i++) {
                var id = $scope.users[i].id;
                if ($scope.selected.users.indexOf(id) != -1) {
                    $scope.users[i].loading = 1;
                }
            };

            itemService.setEnabledSelected('manager_ws_users_set_enabled',
                $scope.selected.users, enabled).then(function (response) {
                    if (response.data.success) {
                        for (var i = 0; i < $scope.users.length; i++) {
                            var id = $scope.users[i].id;
                            if ($scope.selected.users.indexOf(id) != -1) {
                                $scope.users[i].activated = enabled;
                                delete $scope.users[i].loading;
                            }
                        };
                    }

                    for (var i = 0; i < response.data.messages.length; i++) {
                        var params = {
                            message: response.data.messages[i].text,
                            type:    response.data.messages[i].type
                        };

                        messenger.post(params);
                    };
                });
        }

        /**
         * Changes the sort order.
         *
         * @param string name Field name.
         */
        $scope.sort = function(name) {
            if ($scope.orderBy[name]) {
                if ($scope.orderBy[name] == 'asc') {
                    $scope.orderBy[name] = 'desc';
                } else {
                    $scope.orderBy[name] = 'asc';
                }
            } else {
                $scope.orderBy = {};
                $scope.orderBy[name] = 'asc';
            }
        }

        /**
         * Refresh the list of elements when some parameter changes.
         *
         * @param array newValues The new values
         * @param array oldValues The old values
         */
        $scope.$watch('[criteria, orderBy, epp, page]', function(newValues, oldValues) {
            if (newValues !== oldValues) {
                if (search) {
                    $timeout.cancel(search);
                }

                search = $timeout(function() {
                    list();
                }, 500);
            }
        }, true);

        /**
         * Checks if a user is selected
         *
         * @param string id The group id.
         */
        $scope.isSelected = function(id) {
            return $scope.selected.users.indexOf(id) != -1
        }

        /**
         * Searches instances given a criteria.
         */
        function list() {
            $scope.loading = 1;

            var cleaned = $scope.cleanFilters($scope.criteria);

            // Search by name, domains and contact mail
            if (cleaned.name) {
                cleaned.username = cleaned.name;

                // OR operator
                cleaned.union = 'OR';
            }

            var data = {
                criteria: cleaned,
                orderBy: $scope.orderBy,
                epp: $scope.epp,
                page: $scope.page
            };

            itemService.list('manager_ws_users_list', data).then(function (response) {
                $scope.users   = response.data.results;
                $scope.total   = response.data.total;
                $scope.loading = 0;
            });
        }
    }
);
