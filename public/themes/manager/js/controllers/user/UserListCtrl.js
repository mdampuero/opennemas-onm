/**
 * Handles all actions in users listing.
 *
 * @param  Object $modal       The modal service.
 * @param  Object $scope       The current scope.
 * @param  Object itemService  The item service.
 * @param  Object routing The routing service.
 * @param  Object messenger    The messenger service.
 * @param  Object data         The input data.
 *
 * @return Object The user list controller.
 */
angular.module('ManagerApp.controllers').controller('UserListCtrl', [
    '$modal', '$scope', 'itemService', 'routing', 'messenger', 'data',
    function ($modal, $scope, itemService, routing, messenger, data) {
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
         * The list of selected elements.
         *
         * @type array
         */
        $scope.selected = {
            all: false,
            items: []
        };

        /**
         * The listing order.
         *
         * @type Object
         */
        $scope.orderBy = [ { name: 'name', value: 'asc' } ];

        /**
         * The current pagination status.
         *
         * @type Object
         */
        $scope.pagination = {
            epp:   data.epp ? parseInt(data.epp) : 25,
            page:  data.page ? parseInt(data.page) : 1,
            total: data.total
        };

        /**
         * List of template parameters
         *
         * @type Object
         */
        $scope.template = data.template;

        /**
         * List of available users.
         *
         * @type Object
         */
        $scope.items = data.results;

        /**
         * Confirm delete action.
         */
        $scope.delete = function(user) {
            var modal = $modal.open({
                templateUrl: 'modal-confirm',
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
                        };
                    }
                }
            });

            modal.result.then(function (response) {
                if (response) {
                    messenger.post({
                        message: response.data,
                        type: response.status == 200 ? 'success' : 'error'
                    });

                    if (response.status == 200) {
                        list();
                    }
                }
            });
        };

        /**
         * Confirm delete action.
         */
        $scope.deleteSelected = function() {
            var modal = $modal.open({
                templateUrl: 'modal-confirm',
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
                                $scope.selected.items);
                        };
                    }
                }
            });

            modal.result.then(function (response) {
                if (response.status == 200 || response.status == 207) {
                    list();

                    $scope.selected = {
                        all: false,
                        items: []
                    };

                    // Show success message
                    if (response.data.success.ids.length > 0) {
                        messenger.post({
                            message: response.data.success.message,
                            type: 'success'
                        });
                    }

                    // Show errors messages
                    for (var i = 0; i < response.data.errors.length; i++) {
                        var params = {
                            message: response.data.error[i].message,
                            type:    'error'
                        };

                        messenger.post(params);
                    }
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
            while (i < $scope.orderBy.length && $scope.orderBy[i].name != name) {
                i++;
            }

            if (i < $scope.orderBy.length) {
                return $scope.orderBy[i].value;
            }

            return false;
        };

        /**
         * Checks if a user is selected
         *
         * @param string id The group id.
         */
        $scope.isSelected = function(id) {
            return $scope.selected.items.indexOf(id) != -1;
        };

        /**
         * Reloads the listing.
         */
        $scope.refresh = function() {
            search = list();
        };

        /**
         * Reloads the list on keypress.
         *
         * @param  Object event The even object.
         */
        $scope.searchByKeypress = function(event) {
            if (event.keyCode == 13) {
                if ($scope.pagination.page != 1) {
                    $scope.pagination.page = 1;
                } else {
                    list();
                }
            }
        };

        /**
         * Selects/unselects all users.
         */
        $scope.selectAll = function() {
            if ($scope.selected.all) {
                $scope.selected.items = $scope.items.map(function(user) {
                    return user.id;
                });
            } else {
                $scope.selected.items = [];
            }
        };

        /**
         * Enables/disables an user.
         *
         * @param boolean enabled Instance activated value.
         */
        $scope.setEnabled = function(user, enabled) {
            user.loading = 1;

            itemService.patch('manager_ws_user_patch',
                user.id, { activated: enabled }).then(function (response) {
                    user.loading = 0;

                    messenger.post({
                        message: response.data,
                        type: response.status == 200 ? 'success' : 'error'
                    });

                    if (response.status == 200) {
                        user.activated = enabled;
                    }
                });
        };

        /**
         * Enables/disables the selected users.
         *
         * @param integer enabled The activated value.
         */
        $scope.setEnabledSelected = function(enabled) {
            for (var i = 0; i < $scope.items.length; i++) {
                var id = $scope.items[i].id;
                if ($scope.selected.items.indexOf(id) != -1) {
                    $scope.items[i].loading = 1;
                }
            }

            var data = {
                selected: $scope.selected.items,
                activated: enabled
            }

            itemService.patchSelected('manager_ws_users_patch', data).then(function (response) {
                if (response.status == 200 || response.status == 207) {
                    // Update users changed successfully
                    for (var i = 0; i < $scope.items.length; i++) {
                        var id = $scope.items[i].id;

                        if (response.data.success.ids.indexOf(id) != -1) {
                            $scope.items[i].activated = enabled;
                            delete $scope.items[i].loading;
                        }
                    }

                    // Show success message
                    if (response.data.success.ids.length > 0)
                    messenger.post({
                        message: response.data.success.message,
                        type: 'success'
                    });

                    // Show errors
                    for (var i = 0; i < response.data.errors.length; i++) {
                        var params = {
                            message: response.data.error[i].message,
                            type:    'error'
                        };

                        messenger.post(params);
                    }
                }
            });
        };

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

            $scope.pagination.page = 1;
        }

        /**
         * Marks variables to delete for garbage collector;
         */
        $scope.$on('$destroy', function() {
            $scope.criteria         = null;
            $scope.pagination.epp   = null;
            $scope.items            = null;
            $scope.selected         = null;
            $scope.orderBy          = null;
            $scope.pagination.page  = null;
            $scope.pagination.total = null;
        })

        /**
         * Refresh the list of elements when some parameter changes.
         *
         * @param array newValues The new values
         * @param array oldValues The old values
         */
        $scope.$watch('[criteria.fk_user_group, orderBy, pagination.epp, pagination.page]', function(newValues, oldValues) {
            if (newValues !== oldValues) {
                list();
            }
        }, true);

        /**
         * Searches users given a criteria.
         *
         * @return Object The function to execute past 500 ms.
         */
        function list() {
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
                orderBy:  $scope.orderBy,
                epp:      $scope.pagination.epp,
                page:     $scope.pagination.page
            };

            itemService.encodeFilters($scope.criteria, $scope.orderBy,
                $scope.pagination.epp, $scope.pagination.page);

            itemService.list('manager_ws_users_list', data).then(
                function (response) {
                    $scope.items   = response.data.results;
                    $scope.pagination.total   = response.data.total;
                    $scope.loading = 0;

                    // Scroll top
                    $(".page-content").animate({ scrollTop: "0px" }, 1000);
                }
            );
        }

        // Initialize filters from URL
        var filters = itemService.decodeFilters();
        for(var name in filters) {
            $scope[name] = filters[name];
        }
    }
]);
