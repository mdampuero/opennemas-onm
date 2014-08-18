
angular.module('ManagerApp.controllers').controller('InstanceListCtrl',
    function ($scope, $timeout, itemService, fosJsRouting, data) {
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
            name: 1,
            domains: 1,
            contact_mail: 1,
            last_login: 1,
            created: 1,
            contents: 1,
            alexa: 0,
            page_views: 0
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
            'last_login': 'asc'
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
                    var cleaned = cleanFilters($scope.criteria);

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
                    });
                }, 500);
            }
        }, true);

        /**
         * Cleans the criteria for the current listing.
         *
         * @param Object criteria The search criteria.
         *
         * @return Object The cleaned criteria.
         */
        function cleanFilters(criteria) {
            var cleaned = {};

            for (var name in criteria) {
                for (var i = 0; i < criteria[name].length; i++) {
                    if (criteria[name][i]['value'] != -1
                        && criteria[name][i]['value'] !== ''
                    ){
                        if (criteria[name][i]['operator']) {
                            var values = criteria[name][i]['value'].split(' ');

                            cleaned[name] = [];
                            for (var i = 0; i < values.length; i++) {

                                cleaned[name][i] = {
                                    value: '%' + values[i] + '%',
                                    operator: 'LIKE'
                                };
                            };
                        } else {
                            if (!cleaned[name]) {
                                cleaned[name] = [];
                            }

                            cleaned[name][i] = {
                                value: criteria[name][i]['value']
                            };
                        }
                    }
                }
            };

            return cleaned;
        }
    }
);
