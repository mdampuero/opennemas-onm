/**
 * Handles all actions in instances listing.
 *
 * @param  Object $modal       The modal service.
 * @param  Object $scope       The current scope.
 * @param  Object $timeout     The timeout service.
 * @param  Object itemService  The item service.
 * @param  Object fosJsRouting The fosJsRouting service.
 * @param  Object messenger    The messenger service.
 * @param  Object data         The input data.
 *
 * @return Object The instance list controller.
 */
angular.module('ManagerApp.controllers').controller('InstanceListCtrl',
    function ($modal, $scope, $timeout, itemService, fosJsRouting, messenger, data) {
        /**
         * The criteria to search.
         *
         * @type Object
         */
        $scope.criteria = {
            name: [ { value: '', operator: 'like' } ]
        };

        /**
         * The visible table columns.
         *
         * @type Object
         */
        $scope.columns = {
            name:         1,
            domains:      1,
            contact_mail: 1,
            last_login:   1,
            created:      1,
            contents:     1,
            alexa:        0,
            page_views:   0
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
            'last_login': 'asc'
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
         * Variable to store the current search.
         */
        var search;

        /**
         * Flag to know if it is the first run.
         *
         * @type boolean
         */
        var init = true;

        /**
         * Confirm delete action.
         */
        $scope.delete = function(instance) {
            var modal =  $modal.open({
                templateUrl: '/managerws/template/common:modal_confirm_delete.tpl',
                controller:  'InstanceModalCtrl',
                resolve: {
                    selected: function() {
                        return instance;
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
                controller:  'InstanceModalCtrl',
                resolve: {
                    selected: function() {
                        return $scope.selected.instances;
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
         * Searches instances given a criteria.
         */
        function list() {
            $scope.loading = 1;

            var cleaned = $scope.cleanFilters($scope.criteria);

            // Search by name, domains and contact mail
            if (cleaned.name) {
                cleaned.domains = cleaned.contact_mail = cleaned.name;

                // OR operator
                cleaned.union = 'OR';
            }

            var data = {
                criteria: cleaned,
                orderBy: $scope.orderBy,
                epp: $scope.epp,
                page: $scope.page
            };

            itemService.list('manager_ws_instances_list', data).then(function (response) {
                $scope.instances = response.data.results;
                $scope.total = response.data.total;

                $scope.loading = 0;
            });
        }
    }
);
