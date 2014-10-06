/**
 * Handles all actions in user groups listing.
 *
 * @param  Object $modal       The modal service.
 * @param  Object $scope       The current scope.
 * @param  Object $timeout     The timeout service.
 * @param  Object itemService  The item service.
 * @param  Object fosJsRouting The fosJsRouting service.
 * @param  Object messenger    The messenger service.
 * @param  Object data         The input data
 *
 * @return Object The controller for user groups.
 */
angular.module('ManagerApp.controllers').controller('UserGroupListCtrl', [
    '$modal', '$scope', '$timeout', 'itemService', 'fosJsRouting', 'messenger', 'data',
    function ($modal, $scope, $timeout, itemService, fosJsRouting, messenger, data) {

        /**
         * The criteria to search.
         *
         * @type Object
         */
        $scope.criteria = {
            name_like: [ { value: '', operator: 'like' } ]
        };

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
        $scope.groups = data.results;

        /**
         * The list of selected elements.
         *
         * @type array
         */
        $scope.selected = {
            all: false,
            groups: []
        };

        /**
         * The number of total items.
         *
         * @type integer
         */
        $scope.total = data.total;

        /**
         * The listing order.
         *
         * @type Object
         */
        $scope.orderBy = {
            'name': 'asc'
        }

        /**
         * The current page
         *
         * @type integer
         */
        $scope.page = 1;

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
        $scope.delete = function(group) {
            var modal = $modal.open({
                templateUrl: '/managerws/template/common:modal_confirm.tpl',
                backdrop: 'static',
                controller: 'modalCtrl',
                resolve: {
                    template: function() {
                        return {
                            name: 'delete-user-group'
                        };
                    },
                    success: function() {
                        return function() {
                            return itemService.delete(
                                'manager_ws_user_group_delete', group.id);
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
                            name: 'delete-user-groups'
                        };
                    },
                    success: function() {
                        return function() {
                            return itemService.deleteSelected(
                                'manager_ws_user_groups_delete',
                                $scope.selected.groups);
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
         * Selects/unselects all groups.
         */
        $scope.selectAll = function() {
            if ($scope.selected.all) {
                $scope.selected.groups = $scope.groups.map(function(group) {
                    return group.id;
                });
            } else {
                $scope.selected.groups = [];
            }
        };

        /**
         * Checks if a group is selected
         *
         * @param string id The group id.
         */
        $scope.isSelected = function(id) {
            return $scope.selected.groups.indexOf(id) != -1
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
         * Frees up memory before controller destroy event
         */
        $scope.$on('$destroy', function() {
            $scope.criteria = null;
            $scope.epp      = null;
            $scope.groups   = null;
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
        $scope.$watch('[orderBy, epp, page]', function(newValues, oldValues) {
            if (newValues !== oldValues) {
                if (search) {
                    $timeout.cancel(search);
                }

                search = list();
            }
        }, true);

        /**
         * Searches groups given a criteria.
         *
         * @return Object The function to execute past 500 ms.
         */
        function list() {
            return $timeout(function() {
                $scope.loading = 1;

                var cleaned = itemService.cleanFilters($scope.criteria);

                var data = {
                    criteria: cleaned,
                    orderBy:  $scope.orderBy,
                    epp:      $scope.epp,
                    page:     $scope.page
                };

                itemService.encodeFilters($scope.criteria, $scope.orderBy,
                    $scope.epp, $scope.page);

                itemService.list('manager_ws_user_groups_list', data).then(
                    function (response) {
                        $scope.groups  = response.data.results;
                        $scope.total   = response.data.total;
                        $scope.loading = 0;

                        // Scroll top
                        $(".page-content").animate({ scrollTop: "0px" }, 1000);
                    }
                );
            }, 500);
        }
    }
]);
