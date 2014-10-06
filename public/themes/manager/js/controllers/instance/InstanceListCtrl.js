/**
 * Handles all actions in instances listing.
 *
 * @param Object $anchorScroll The anchor scroll service.
 * @param Object $location     The location service.
 * @param Object $modal        The modal service.
 * @param Object $scope        The current scope.
 * @param Object $timeout      The timeout service.
 * @param Object itemService   The item service.
 * @param Object fosJsRouting  The fosJsRouting service.
 * @param Object messenger     The messenger service.
 * @param Object data          The input data.
 *
 * @return Object The instance list controller.
 */
angular.module('ManagerApp.controllers').controller('InstanceListCtrl', [
    '$modal', '$scope', '$timeout', 'itemService','fosJsRouting', 'messenger', 'data',
    function ($modal, $scope, $timeout, itemService, fosJsRouting, messenger, data) {
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
            name:       1,
            domains:    1,
            last_login: 1,
            created:    1,
            articles:   1,
            alexa:      1,
            activated:  1
        }

        /**
         * The number of elements per page
         *
         * @type integer
         */
        $scope.epp  = 25;

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
        $scope.orderBy = {
            'last_login': 'desc'
        }

        /**
         * The current page
         *
         * @type integer
         */
        $scope.page = 1;

        /**
         * The number of total items.
         *
         * @type integer
         */
        $scope.total = data.total;

        /**
         * Default join operator for filters.
         *
         * @type string
         */
        $scope.union = 'OR';

        /**
         * Variable to store the current search.
         */
        var search;

        /**
         * Checks if an instance is selected
         *
         * @param string id The group id.
         */
        $scope.isSelected = function(id) {
            return $scope.selected.instances.indexOf(id) != -1
        }

        /**
         * Confirm delete action.
         */
        $scope.delete = function(instance) {
            var modal = $modal.open({
                templateUrl: '/managerws/template/common:modal_confirm.tpl',
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
                                'manager_ws_instance_delete', instance.id);
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
                        var selected = [];

                        for (var i = 0; i < $scope.instances.length; i++) {
                            if ($scope.selected.instances.indexOf(
                                    $scope.instances[i].id) != -1) {
                                selected.push($scope.instances[i]);
                            }
                        };

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

            itemService.setEnabled('manager_ws_instance_set_enabled',
                instance.id, enabled).then(function (response) {
                    instance.loading = 0;

                    if (response.data.success) {
                        instance.activated = enabled;
                    }

                    messenger.post({
                        message: response.data.message.text,
                        type:    response.data.message.type
                    });
                });
        }

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
            };

            itemService.setEnabledSelected('manager_ws_instances_set_enabled',
                $scope.selected.instances, enabled).then(function (response) {
                    if (response.data.success) {
                        for (var i = 0; i < $scope.instances.length; i++) {
                            var id = $scope.instances[i].id;
                            if ($scope.selected.instances.indexOf(id) != -1) {
                                $scope.instances[i].activated = enabled;
                                delete $scope.instances[i].loading;
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
                    delete $scope.orderBy[name];
                }
            } else {
                $scope.orderBy[name] = 'asc';
            }

            $scope.page = 1;
        }

        /**
         * Marks variables to delete for garbage collector;
         */
        $scope.$on('$destroy', function() {
            $scope.criteria  = null;
            $scope.columns   = null;
            $scope.epp       = null;
            $scope.instances = null;
            $scope.selected  = null;
            $scope.orderBy   = null;
            $scope.page      = null;
            $scope.total     = null;
        });

        /**
         * Refresh the list of elements when some parameter changes.
         *
         * @param array newValues The new values
         * @param array oldValues The old values
         */
        $scope.$watch('[orderBy, epp, page]', function(newValues, oldValues) {
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
                    orderBy: $scope.orderBy,
                    epp: $scope.epp,
                    page: $scope.page
                };

                itemService.encodeFilters($scope.criteria, $scope.orderBy,
                    $scope.epp, $scope.page, $scope.union);

                itemService.list('manager_ws_instances_list', data).then(
                    function (response) {
                        $scope.instances = response.data.results;
                        $scope.total = response.data.total;

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
