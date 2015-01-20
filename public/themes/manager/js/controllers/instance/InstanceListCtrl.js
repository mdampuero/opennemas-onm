/**
 * Handles all actions in instances listing.
 *
 * @param Object $modal        The modal service.
 * @param Object $scope        The current scope.
 * @param Object itemService   The item service.
 * @param Object routing       The routing service.
 * @param Object messenger     The messenger service.
 * @param Object webStorage    The web storage service.
 * @param Object data          The input data.
 *
 * @return Object The instance list controller.
 */
angular.module('ManagerApp.controllers').controller('InstanceListCtrl', [
    '$modal', '$scope', 'itemService', 'routing', 'messenger', 'webStorage', 'data',
    function ($modal, $scope, itemService, routing, messenger, webStorage, data) {
        /**
         * The criteria to search.
         *
         * @type Object
         */
        $scope.criteria = {
            name_like: []
        };

        /**
         * The visible table columns.
         *
         * @type Object
         */
        $scope.columns = {
            collapsed: 1,
            selected: [ 'name', 'domains', 'last_login', 'created', 'articles',
                'alexa', 'activated' ]
        };

        /**
         * The list of elements.
         *
         * @type Object
         */
        $scope.instances = data.results;

        /**
         * The list of selected elements.
         *
         * @type array
         */
        $scope.selected = {
            all: false,
            instances: []
        };

        /**
         * The listing order.
         *
         * @type Object
         */
        $scope.orderBy = [ { name: 'last_login', value: 'desc' } ];

        /**
         * The listing order for UI
         */
        $scope.orderUI = {};

        /**
         * The current pagination status.
         *
         * @type Object
         */
        $scope.pagination = {
            epp:   data.epp ? parseInt(data.epp) : 25,
            page:  data.page ? parseInt(data.page) : 1,
            total: data.total
        }

        /**
         * Default join operator for filters.
         *
         * @type string
         */
        $scope.union = 'OR';

        /**
         * Checks if a columns is selected.
         *
         * @param string id The columns name.
         */
        $scope.isEnabled = function(id) {
            return $scope.columns.selected.indexOf(id) != -1;
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
         * Checks if an instance is selected.
         *
         * @param string id The group id.
         */
        $scope.isSelected = function(id) {
            return $scope.selected.instances.indexOf(id) != -1;
        };

        /**
         * Confirm delete action.
         */
        $scope.delete = function(instance) {
            var modal = $modal.open({
                templateUrl: 'modal-confirm',
                backdrop: 'static',
                controller: 'modalCtrl',
                resolve: {
                    template: function() {
                        return {
                            name: 'delete-instance',
                            item: instance
                        };
                    },
                    success: function() {
                        return function() {
                            return itemService.delete(
                                'manager_ws_instance_delete',
                                instance.id
                            );
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
                        var selected = [];

                        for (var i = 0; i < $scope.instances.length; i++) {
                            if ($scope.selected.instances.indexOf(
                                    $scope.instances[i].id) != -1) {
                                selected.push($scope.instances[i]);
                            }
                        }

                        return {
                            name: 'delete-instances',
                            selected: selected
                        };
                    },
                    success: function() {
                        return function() {
                            return itemService.deleteSelected(
                                'manager_ws_instances_delete',
                                $scope.selected.instances);
                        };
                    }
                }
            });

            modal.result.then(function (response) {
                if (response.status == 200 || response.status == 207) {
                    list();

                    $scope.selected = {
                        all: false,
                        instances: []
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
         * Reloads the listing.
         */
        $scope.refresh = function() {
            list();
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
         * Selects/unselects all instances.
         */
        $scope.selectAll = function() {
            if ($scope.selected.all) {
                $scope.selected.instances = $scope.instances.map(function(instance) {
                    return instance.id;
                });
            } else {
                $scope.selected.instances = [];
            }
        };

        /**
         * Enables/disables an instance.
         *
         * @param boolean enabled Instance activated value.
         */
        $scope.setEnabled = function(instance, enabled) {
            instance.loading = 1;

            itemService.patch('manager_ws_instance_patch',
                instance.id, { activated: enabled }).then(function (response) {
                    instance.loading = 0;

                    messenger.post({
                        message: response.data,
                        type: response.status == 200 ? 'success' : 'error'
                    });

                    if (response.status == 200) {
                        instance.activated = enabled;
                    }
                });
        };

        /**
         * Enables/disables the selected instances.
         *
         * @param integer enabled The activated value.
         */
        $scope.setEnabledSelected = function(enabled) {
            for (var i = 0; i < $scope.instances.length; i++) {
                var id = $scope.instances[i].id;
                if ($scope.selected.instances.indexOf(id) != -1) {
                    $scope.instances[i].loading = 1;
                }
            }

            var data = {
                selected: $scope.selected.instances,
                activated: enabled
            }

            itemService.patchSelected('manager_ws_instances_patch', data).then(function (response) {
                if (response.status == 200 || response.status == 207) {
                    // Update instances changed successfully
                    for (var i = 0; i < $scope.instances.length; i++) {
                        var id = $scope.instances[i].id;

                        if (response.data.success.ids.indexOf(id) != -1) {
                            $scope.instances[i].activated = enabled;
                            delete $scope.instances[i].loading;
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
        };

        /**
         * Toggles column filters container.
         */
        $scope.toggleColumns = function () {
            $scope.columns.collapsed = !$scope.columns.collapsed;

            if (!$scope.columns.collapsed) {
                $scope.scrollTop();
            }
        };

        /**
         * Marks variables to delete for garbage collector;
         */
        $scope.$on('$destroy', function() {
            $scope.criteria         = null;
            $scope.columns          = null;
            $scope.pagination.epp   = null;
            $scope.instances        = null;
            $scope.selected         = null;
            $scope.orderBy          = null;
            $scope.pagination.page  = null;
            $scope.pagination.total = null;
        });

        /**
         * Refresh the list of elements when some parameter changes.
         *
         * @param array newValues The new values
         * @param array oldValues The old values
         */
        $scope.$watch('[orderBy, pagination.epp, pagination.page]', function(newValues, oldValues) {
            if (newValues !== oldValues) {
                list();
            }
        }, true);

        /**
         * Updates the columns stored in localStorage.
         *
         * @param Object newValues New values.
         * @param Object oldValues Old values.
         */
        $scope.$watch('columns', function(newValues, oldValues) {
            if (newValues != oldValues) {
                webStorage.local.add('instances-columns', $scope.columns);
            }
        }, true);

        /**
         * Searches instances given a criteria.
         *
         * @return Object The function to execute past 500 ms.
         */
        function list() {
            $scope.loading = 1;

            // Search by name, domains and contact mail
            if ($scope.criteria.name_like) {
                $scope.criteria.domains_like =
                    $scope.criteria.contact_mail_like =
                        $scope.criteria.name_like;
            }

            var cleaned = itemService.cleanFilters($scope.criteria);

            if (cleaned.name && cleaned.domains && cleaned.contact_mail) {
                // OR operator
                cleaned.union = $scope.union;
            }

            var data = {
                criteria: cleaned,
                orderBy:  $scope.orderBy,
                epp:      $scope.pagination.epp,
                page:     $scope.pagination.page
            };

            itemService.encodeFilters($scope.criteria, $scope.orderBy,
                $scope.pagination.epp, $scope.pagination.page, $scope.union);

            itemService.list('manager_ws_instances_list', data).then(
                function (response) {
                    $scope.instances        = response.data.results;
                    $scope.pagination.total = response.data.total;

                    $scope.loading = 0;

                    // Scroll top
                    $(".page-content").animate({ scrollTop: "0px" }, 1000);
                }
            );
        }

        // Initialize filters from URL
        var filters = itemService.decodeFilters();
        for (var name in filters) {
            $scope[name] = filters[name];
        }

        // Get enabled columns from localStorage
        if (webStorage.local.get('instances-columns')) {
            $scope.columns = webStorage.local.get('instances-columns');
        }

        if (webStorage.local.get('token')) {
            $scope.token = webStorage.local.get('token');
        }
    }
]);
