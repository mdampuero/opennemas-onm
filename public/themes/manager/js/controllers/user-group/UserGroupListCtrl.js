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
angular.module('ManagerApp.controllers').controller('UserGroupListCtrl',
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
            var modal =  $modal.open({
                templateUrl: '/managerws/template/common:modal_confirm_delete.tpl',
                controller:  'UserGroupModalCtrl',
                resolve: {
                    selected: function() {
                        return group;
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
                controller:  'UserGroupModalCtrl',
                resolve: {
                    selected: function() {
                        return $scope.selected.groups;
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
         * Searches groups given a criteria.
         */
        function list() {
            $scope.loading = 1;

            var cleaned = $scope.cleanFilters($scope.criteria);
            var data = {
                criteria: cleaned,
                orderBy:  $scope.orderBy,
                epp:      $scope.epp,
                page:     $scope.page
            };

            itemService.list('manager_ws_user_groups_list', data).then(function (response) {
                $scope.groups  = response.data.results;
                $scope.total   = response.data.total;
                $scope.loading = 0;
            });
        }
    }
);
