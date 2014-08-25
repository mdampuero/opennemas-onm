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
                $scope.users = response.data.results;
                $scope.total = response.data.total;
            });
        }
    }
);
