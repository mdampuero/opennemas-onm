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
angular.module('ManagerApp.controllers').controller('UserListCtrl', [
    '$modal', '$scope', '$timeout', 'itemService', 'fosJsRouting', 'messenger', 'data',
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
        $scope.orderBy = [ { name: 'name', value: 'asc' } ];

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
            var modal = $modal.open({
                templateUrl: '/managerws/template/common:modal_confirm.tpl',
                backdrop: 'static',
                controller: 'modalCtrl',
                resolve: {
                    template: function() {
                        return {
                            name: 'delete-user'
                        };
                    },
                    success: function() {
                        return function() {
                            return itemService.delete(
                                'manager_ws_user_delete', user.id);
                        }
                    }
                }
            });

            modal.result.then(function (response) {
                if (response.data.success) {
                    if (response.data.message) {
                        messenger.post({
                            message: response.data.message.text,
                            type:    response.data.message.type
                        });
                    };

                    list();
                }
            });
        };

        /**
         * Confirm delete action.
         */
        $scope.deleteSelected = function() {
            var modal = $modal.open({
                templateUrl: '/managerws/template/common:modal_confirm.tpl',
                backdrop: 'static',
                controller: 'modalCtrl',
                resolve: {
                    template: function() {
                        return {
                            name: 'delete-users'
                        };
                    },
                    success: function() {
                        return function() {
                            return itemService.deleteSelected(
                                'manager_ws_users_delete',
                                $scope.selected.users);
                        }
                    }
                }
            });

            modal.result.then(function (response) {
                if (response.data) {
                    for (var i = 0; i < response.data.messages.length; i++) {
                        messenger.post({
                            message: response.data.messages[i].text,
                            type:    response.data.messages[i].type
                        });
                    };

                    list();
                }
            });
        };

        /**
         * Checks if the listing is ordered by the given field name.
         *
         * @param string name The field name.
         *
         * @return mixed The order value, if the order exists. Otherwise,
         *               returns false.
         */
        $scope.isOrderedBy = function(name) {
            var i = 0;
            while (i < $scope.orderBy.length
                    && $scope.orderBy[i].name != name) {
                i++;
            }

            if (i < $scope.orderBy.length) {
                return $scope.orderBy[i].value;
            }

            return false;
        }

        /**
         * Checks if a user is selected
         *
         * @param string id The group id.
         */
        $scope.isSelected = function(id) {
            return $scope.selected.users.indexOf(id) != -1
        }

        /**
         * Reloads the list on keypress.
         *
         * @param  Object event The even object.
         */
        $scope.searchByKeypress = function(event) {
            if (event.keyCode == 13) {
                $scope.page = 1;

                if (search) {
                    $timeout.cancel(search);
                }

                search = list();
            };
        }

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
            var i = 0;
            while (i < $scope.orderBy.length && $scope.orderBy[i].name != name) {
                i++;
            }

            if (i >= $scope.orderBy.length) {
                $scope.orderBy.push({ name: name, value: 'asc' });
            } else {
                if ($scope.orderBy[i].value == 'asc') {
                    $scope.orderBy[i].value = 'desc';
                } else {
                    $scope.orderBy.splice(i, 1);
                }
            }

            $scope.page = 1;
        }

        /**
         * Marks variables to delete for garbage collector;
         */
        $scope.$on('$destroy', function() {
            $scope.criteria = null;
            $scope.epp      = null;
            $scope.users    = null;
            $scope.selected = null;
            $scope.orderBy  = null;
            $scope.page     = null;
            $scope.total    = null;
        })

        /**
         * Refresh the list of elements when some parameter changes.
         *
         * @param array newValues The new values
         * @param array oldValues The old values
         */
        $scope.$watch('[criteria.fk_user_group, orderBy, epp, page]', function(newValues, oldValues) {
            if (newValues !== oldValues) {
                if (search) {
                    $timeout.cancel(search);
                }

                search = list();
            }
        }, true);

        /**
         * Searches instances given a criteria.
         *
         * @return Object The function to execute past 500 ms.
         */
        function list() {
            return $timeout(function() {
                $scope.loading = 1;

                var cleaned = itemService.cleanFilters($scope.criteria);

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

                itemService.encodeFilters($scope.criteria, $scope.orderBy,
                    $scope.epp, $scope.page);

                itemService.list('manager_ws_users_list', data).then(
                    function (response) {
                        $scope.users   = response.data.results;
                        $scope.total   = response.data.total;
                        $scope.loading = 0;

                        // Scroll top
                        $(".page-content").animate({ scrollTop: "0px" }, 1000);
                    }
                );
            }, 500);
        }

        // Initialize filters from URL
        var filters = itemService.decodeFilters();
        for(var name in filters) {
            $scope[name] = filters[name];
        }
    }
]);
