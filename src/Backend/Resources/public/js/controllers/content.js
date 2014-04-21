/**
 * Controller to handle list actions.
 */
function ContentCtrl($http, $location, $modal, $scope, $timeout, $translate, fosJsRouting, sharedVars, messenger, paginationConfig) {
    /**
     * All contents selected flag.
     *
     * @type boolean
     */
    $scope.allSelected = false;

    /**
     * Object with the filters of the current list.
     *
     * @type object
     */
    $scope.shvs = {}

    /**
     * Removes invalid filters from array of filters.
     *
     * @param  Object $filters Filters.
     * @return Object          Filters after removing invalid properties.
     */
    function clearFilters(filters) {
        var cleaned = {};
        for (var name in filters) {
            for (var i = 0; i < filters[name].length; i++) {
                if (filters[name][i]['value'] != -1
                    && filters[name][i]['value'] !== ''
                ){
                    if (name.indexOf('_like') !== -1) {
                        var shortName = name.substring(0, name.indexOf('_like'));
                        cleaned[shortName] = [];
                        cleaned[shortName][i] = {
                            value: '%' + filters[name][i]['value'] + '%'
                        };
                    } else {
                        if (!cleaned[name]) {
                            cleaned[name] = [];
                        }

                        cleaned[name][i] = {
                            value: filters[name][i]['value']
                        };
                    }
                }
            }
        };

        return cleaned;
    }

    /**
     * Goes to content edit page.
     *
     * @param int    id    Content id.
     * @param string route Route name.
     */
    $scope.edit = function(id, route) {
        return fosJsRouting.generate(route, { id: id});
    }

    /**
     * Initializes the content type for the current list.
     *
     * @param string content Content type.
     * @param object filters Object of allowed filters.
     * @param string sortBy  Field name to sort by.
     * @param string route   Route name.
     */
    $scope.init = function(content, filters, sortBy, sortOrder, route, lang) {
        $translate.use(lang ? lang : 'en');
        $translate(['Next', 'Previous']).then(function (translations) {
            paginationConfig.nextText     = translations.Next;
            paginationConfig.previousText = translations.Previous;
        });

        // Initialize the current list.
        sharedVars.set('contentType', content);
        sharedVars.set('page', 1);
        sharedVars.set('elements_per_page', 10);

        // Load filters from URL
        var query = $location.search();
        for (var name in query) {
            if (name == 'page') {
                sharedVars.set('page', query[name]);
            } else if (name == 'view') {
                sharedVars.set('elements_per_page', query[name]);
            } else if (filters[name] != null) {
                filters[name] = query[name];
            }
        };

        // Add content_type_name if it isn't a list of all content types
        if (content != null && content != 'content') {
            filters['content_type_name'] = content;
        }

        // Set sortBy
        sharedVars.set('sort_by', 'created');
        if (sortBy != null) {
            sharedVars.set('sort_by', sortBy);
        }

        // Set sortOrder
        sharedVars.set('sort_order', 'desc');
        if (sortOrder != null) {
            sharedVars.set('sort_order', sortOrder);
        }

        // Filters used in request
        sharedVars.set('filters', {});

        // Filters used in GUI
        sharedVars.set('search', filters);


        sharedVars.set('contents', []);
        sharedVars.set('selected', []);

        // Route for list (required by $watch)
        $scope.route = route;
        $scope.list(route);
    }

    /**
     * Updates the array of contents.
     *
     * @param string route Route name.
     */
    $scope.list = function(route) {
        // Enable spinner
        $scope.loading = 1;

        var postData = {
            elements_per_page: $scope.shvs.elements_per_page,
            page:              $scope.shvs.page,
            sort_by:           $scope.shvs.sort_by,
            sort_order:        $scope.shvs.sort_order,
            search:            clearFilters($scope.shvs.filters)
        }

        var url = fosJsRouting.generate(route, { contentType: $scope.shvs.contentType });
        $http.post(url, postData).success(function(response) {
            sharedVars.set('total', response.total);
            sharedVars.set('page', response.page);
            sharedVars.set('contents', response.results);
            sharedVars.set('map', response.map);

            if (response.hasOwnProperty('extra')) {
                sharedVars.set('extra', response.extra);
            };

            // Disable spinner
            $scope.loading = 0;
        }).error(function() {
            // Disable spinner
            $scope.loading = 0;
        });
    }

    /**
     * Opens new modal window when clicking delete button.
     *
     * @param  string template Template id.
     * @param  string route    Route name.
     * @param  int    index    Index of the selected content.
     */
    $scope.open = function(template, route, index) {
        $modal.open({
            templateUrl: template,
            controller: 'ContentModalCtrl',
            resolve: {
                index: function () {
                    return index;
                },
                route: function () {
                    return route;
                }
            }
        });
    }

    /**
     * Saves the content positions in widget.
     *
     * @param string route Route name.
     */
    $scope.savePositions = function(route) {
        // Load shared variable
        var contents = sharedVars.get('contents');
        var ids = [];

        for (var i = 0; i < contents.length; i++) {
            ids.push(contents[i].id);
        };

        var url = fosJsRouting.generate(
            route,
            { contentType: $scope.shvs.contentType }
        );
        $http.post(url, { positions: ids }).success(function(response) {
            if (response.content_status != null) {
                contents[index].content_status = response.content_status;
            }

            for (var i = 0; i < response.messages.length; i++) {
                var params = {
                    id:      new Date().getTime() + '_' + response.messages[i].id,
                    message: response.messages[i].message,
                    type:    response.messages[i].type
                };

                messenger.post(params);
            };
        }).error(function(response) {
        });
    }

    /**
     * Changes the page of the list.
     *
     * @param  int page Page number.
     */
    $scope.selectPage = function(page, route) {
        if (page != $scope.shvs.page) {
            $scope.shvs.page = page;
            $location.search('page', page);
            $scope.list(route);
        }
    };

    /**
     * Updates an item.
     *
     * @param int    index   Index of the item to update in contents.
     * @param int    id      Id of the item to update.
     * @param string route   Route name.
     * @param string name    Name of the property to update.
     * @param mixed  value   New value.
     * @param string loading Name of the property used to show work-in-progress.
     */
    $scope.updateItem = function (index, id, route, name, value, loading) {
        // Load shared variable
        var contents = sharedVars.get('contents');

        // Enable spinner
        contents[index][loading] = 1;

        var url = fosJsRouting.generate(
            route,
            { contentType: $scope.shvs.contentType, id: id }
        );

        $http.post(url, { value: value }).success(function(response) {
            if (response[name] != null) {
                contents[index][name] = response[name];
            }

            for (var i = 0; i < response.messages.length; i++) {
                var params = {
                    id:      new Date().getTime() + '_' + response.messages[i].id,
                    message: response.messages[i].message,
                    type:    response.messages[i].type
                };

                messenger.post(params);
            };

            // Disable spinner
            contents[index][loading] = 0;
        }).error(function(response) {
            // Disable spinner
            contents[index][loading] = 0;
        });

        // Updated shared variable
        sharedVars.set('contents', contents);
    };

    /**
     * Updates selected items.
     *
     * @param string route   Route name.
     * @param string name    Name of the property to update.
     * @param mixed  value   New value.
     * @param string loading Name of the property used to show work-in-progress.
     */
    $scope.updateSelectedItems = function (route, name, value, loading) {
        // Load shared variable
        var contents = sharedVars.get('contents');
        var selected = sharedVars.get('selected');

        updateItemsStatus(loading, 1);

        var url = fosJsRouting.generate(
            route,
            { contentType: $scope.shvs.contentType }
        );
        $http.post(url, { ids: selected, value: value })
            .success(function(response) {
                updateItemsStatus(loading, 0, name, value);

                for (var i = 0; i < response.messages.length; i++) {
                    var params = {
                        id:      new Date().getTime() + '_' + response.messages[i].id,
                        message: response.messages[i].message,
                        type:    response.messages[i].type
                    };

                    messenger.post(params);
                };
            }).error(function(response) {

            });
    };

    /**
     * Updates selected items current status.
     * @param  string  loading Name of the work-in-progress property.
     * @param  integer status  Current work-in-progress status.
     * @param  string  name    Name of the property to update.
     * @param  mixed   value   Value of the property to update.
     */
    function updateItemsStatus(loading, status, name, value) {
        // Load shared variables
        var contents = sharedVars.get('contents');
        var selected = sharedVars.get('selected');

        for (var i = 0; i < selected.length; i++) {
            var j = 0;

            while (j < contents.length && contents[j].id != selected[i]) {
                j++;
            }

            if (j < contents.length) {
                contents[j][loading] = status
                contents[j][name]    = value;
            }
        };

        // Updated shared variable
        sharedVars.set('contents', contents);
        sharedVars.set('selected', selected);
    }

    /**
     * Updates the status property for selected contents.
     *
     * @param int loading Loading flag to use in template.
     * @param int status  Status value.
     */
    function updateStatus(loading, status) {
        // Load shared variable
        var contents = sharedVars.get('contents');
        var selected = sharedVars.get('selected');

        for (var i = 0; i < selected.length; i++) {
            var j = 0;
            while (j < contents.length && contents[j].id != selected[i]) {
                j++;
            }

            if (j < contents.length) {
                contents[j].status = status;
                contents[j].loading        = loading
            }
        };

        // Updated shared variable
        sharedVars.set('contents', contents);
        sharedVars.set('selected', selected);
    }

    /**
     * Reloads the list when filters change.
     *
     * @param  object newValues New filters values.
     * @param  object oldValues Old filters values.
     */
    var searchTimeout;
    $scope.$watch('shvs.search', function(newValues, oldValues) {
        if (searchTimeout) {
            $timeout.cancel(searchTimeout);
        }

        if (newValues !== oldValues) {
            for (var name in newValues) {
                if (newValues[name] !== oldValues[name]) {
                    $location.search(name, newValues[name]);

                    $scope.shvs.filters[name] = [];

                    if ($scope.shvs.search[name] instanceof Array) {
                        for (var i = 0; i < $scope.shvs.search[name].length; i++) {
                            $scope.shvs.filters[name].push({
                                value: $scope.shvs.search[name][i]
                            });
                        };
                    } else {
                        $scope.shvs.filters[name].push({
                            value: $scope.shvs.search[name]
                        });
                    }

                    if ($scope.shvs.search[name] == ''
                        || $scope.shvs.search[name] == -1
                    ) {
                        $location.search(name, null);
                    }
                }
            };

            $scope.shvs.page = 1;
            $location.search('page', null);

            searchTimeout = $timeout(function() {
                $scope.list($scope.route);
            }, 500);
        }
    }, true);

    /**
     * Load the value of shared variables object in scope when it changes.
     *
     * @param  Event  event Event object.
     * @param  Object vars  Shared variables object.
     */
    $scope.$on('SharedVarsChanged', function(event, vars) {
        $scope.shvs = vars;

        for (var name in $scope.shvs.search) {
            $location.search(name, $scope.shvs.search[name]);

            $scope.shvs.filters[name] = [];

            if ($scope.shvs.search[name] instanceof Array) {
                for (var i = 0; i < $scope.shvs.search[name].length; i++) {
                    $scope.shvs.filters[name].push({
                        value: $scope.shvs.search[name][i]
                    });
                };
            } else {
                $scope.shvs.filters[name].push({
                    value: $scope.shvs.search[name]
                });
            }

            if ($scope.shvs.search[name] == ''
                || $scope.shvs.search[name] == -1
            ) {
                $location.search(name, null);
            }
        };
    });
}

// Register ContentCtrl function as AngularJS controller
angular.module('BackendApp.controllers').controller('ContentCtrl', ContentCtrl);
