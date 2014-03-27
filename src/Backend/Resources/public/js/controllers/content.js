/**
 * Controller to handle list actions.
 */
function ContentCtrl($http, $location, $modal, $scope, $timeout, fosJsRouting, sharedVars) {
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
     * Returns if the given content is selected.
     *
     * @return boolean True if the given content is selected. Otherwise, return
     *                 false.
     */
    $scope.areSelected = function() {
        var selected = sharedVars.get('selected') ? sharedVars.get('selected').length : 0;
        var contents = sharedVars.get('contents') ? sharedVars.get('contents').length : 0;

        return selected == contents;
    };

    /**
     * Changes the available property for selected contents.
     *
     * @param int    available Available value.
     * @param string route     Route name.
     */
    $scope.batchSetContentStatus = function (available, route) {
        // Load shared variable
        var selected = sharedVars.get('selected');

        updateContentStatus(1);

        var url = fosJsRouting.generate(route, { contentType: $scope.shvs.contentType });
        $http.post(url, { ids: selected, available: available })
            .success(function(response) {
                updateContentStatus(0, available);
            });
    };

    /**
     * Changes the available property for selected contents.
     *
     * @param int    available Available value.
     * @param string route     Route name.
     */
    $scope.batchToggleStatus = function (status, route) {
        // Load shared variable
        var selected = sharedVars.get('selected');

        updateStatus(1);

        var url = fosJsRouting.generate(route, { contentType: $scope.shvs.contentType });
        $http.post(url, { ids: selected, status: status })
            .success(function(response) {
                updateStatus(0, status);
            });
    };

    /**
     * Changes the in_home property for selected contents.
     *
     * @param int    inHome Available value.
     * @param string route  Route name.
     */
    $scope.batchToggleInHome = function (inHome, route) {
        // Load shared variable
        var selected = sharedVars.get('selected');

        updateInHome(1);

        var url = fosJsRouting.generate(route, { contentType: $scope.shvs.contentType });
        $http.post(url, { ids: selected, in_home: inHome })
            .success(function(response) {
                updateInHome(0, inHome);
            });
    };

    /**
     * Removes invalid filters from array of filters.
     *
     * @param  Object $filters Filters.
     * @return Object          Filters after removing invalid properties.
     */
    function clearFilters(filters) {
        var cleaned = {};
        for (var name in filters) {
            if (filters[name] != -1 && filters[name] !== '') {
                if (name.indexOf('_like') !== -1) {
                    cleaned[name.substring(0, name.indexOf('_like'))] = '%' + filters[name] + '%'
                } else {
                    cleaned[name] = filters[name];
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
        var url = fosJsRouting.generate(route, { contentType: $scope.shvs.contentType, id: id});
        document.location = url;
    }

    /**
     * Initializes the content type for the current list.
     *
     * @param string content Content type.
     * @param object filters Object of allowed filters.
     * @param string sortBy  Field name to sort by.
     * @param string route   Route name.
     */
    $scope.init = function(content, filters, sortBy, sortOrder, route) {
        // Initialize content type for current list.
        sharedVars.set('contentType', content);

        // Load filters from URL
        var query = $location.search();
        for (var name in query) {
            if (filters[name] != null) {
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

        sharedVars.set('search', filters);
        sharedVars.set('page', 1);
        sharedVars.set('elements_per_page', 10);
        sharedVars.set('contents', []);
        sharedVars.set('selected', []);

        // Route for list (required by $watch)
        $scope.route = route;
        $scope.list(route);
    }

    /**
     * Returns if the given content is selected.
     *
     * @param  int     id Checked item id.
     * @return boolean    True if the given content is selected. Otherwise,
     *                    return false.
     */
    $scope.isSelected = function(id) {
        return sharedVars.get('selected').indexOf(id) >= 0;
    };

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
            search:            clearFilters($scope.shvs.search)
        }

        var url = fosJsRouting.generate(route, { contentType: $scope.shvs.contentType });
        $http.post(url, postData).success(function(response) {
            sharedVars.set('total', response.total);
            sharedVars.set('page', response.page);
            sharedVars.set('contents', response.results);
            sharedVars.set('map', response.map);

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
     * Selects all contents in the list.
     *
     * @param object $event The event.
     */
    $scope.selectAll = function ($event) {
        var checkbox = $event.target;

        $scope.allSelected = !$scope.areSelected();
        sharedVars.set('selected', []);

        if ($scope.allSelected) {
            for (var content in $scope.shvs.contents) {
                updateSelected($scope.allSelected, $scope.shvs.contents[content].id);
            };
        }
    };

    /**
     * Changes the page of the list.
     *
     * @param  int page Page number.
     */
    $scope.selectPage = function(page, route) {
        if (page != $scope.shvs.page) {
            $scope.shvs.page = page;
            $scope.list(route);
        }
    };

    /**
     * Changes the content available property.
     *
     * @param int    id    Content id.
     * @param int    index Index of content in the array of contents.
     * @param string route Route name.
     */
    $scope.setContentStatus = function (index, route, status) {
        // Load shared variable
        var contents = sharedVars.get('contents');

        // Enable spinner
        contents[index].loading = 1;

        var url = fosJsRouting.generate(
            route,
            { contentType: $scope.shvs.contentType, id: contents[index].id }
        );
        $http.post(url, {status: status}).success(function(response) {
            if (response.content_status != null) {
                contents[index].content_status = response.content_status;
            }

            // Disable spinner
            contents[index].loading = 0;
        }).error(function(response) {
            // Disable spinner
            contents[index].loading = 0;
        });

        // Updated shared variable
        sharedVars.set('contents', contents);
    };

    /**
     * Changes the content in_home property.
     *
     * @param int    id    Content id.
     * @param int    index Index of content in the array of contents.
     * @param string route Route name.
     */
    $scope.toggleInHome = function (id, index, route) {
        // Load shared variable
        var contents = sharedVars.get('contents');

        // Enable spinner
        contents[index].home_loading = 1;

        var url = fosJsRouting.generate(route, { contentType: $scope.shvs.contentType, id: id });
        $http.post(url).success(function(response) {
            if (response.in_home != null) {
                contents[index].in_home = response.in_home;
            }

            // Disable spinner
            contents[index].home_loading = 0;
        }).error(function(response) {
            // Disable spinner
            contents[index].home_loading = 0;
        });

        // Updated shared variable
        sharedVars.set('contents', contents);
    };

    /**
     * Changes the content favorite property.
     *
     * @param int    id    Content id.
     * @param int    index Index of content in the array of contents.
     * @param string route Route name.
     */
    $scope.toggleFavorite = function (id, index, route) {
        // Load shared variable
        var contents = sharedVars.get('contents');

        // Enable spinner
        contents[index].favorite_loading = 1;

        var url = fosJsRouting.generate(route, { contentType: $scope.shvs.contentType, id: id });
        $http.post(url).success(function(response) {
            if (response.favorite != null) {
                contents[index].favorite = response.favorite;
            }

            // Disable spinner
            contents[index].favorite_loading = 0;
        }).error(function(response) {
            // Disable spinner
            contents[index].favorite_loading = 0;
        });

        // Updated shared variable
        sharedVars.set('contents', contents);
    };

    /**
     * Changes the content available property.
     *
     * @param int    id    Content id.
     * @param int    index Index of content in the array of contents.
     * @param string route Route name.
     */
    $scope.toggleStatus = function (id, index, route) {
        // Load shared variable
        var contents = sharedVars.get('contents');

        // Enable spinner
        contents[index].loading = 1;

        var url = fosJsRouting.generate(route, { contentType: $scope.shvs.contentType, id: id });
        $http.post(url).success(function(response) {
            if (response.status != null) {
                contents[index].status = response.status;
            }

            // Disable spinner
            contents[index].loading = 0;
        }).error(function(response) {
            // Disable spinner
            contents[index].loading = 0;
        });

        // Updated shared variable
        sharedVars.set('contents', contents);
    };

    /**
     * Updates the content_status property for selected contents.
     *
     * @param int loading   Loading flag to use in template.
     * @param int content_status Available value.
     */
    function updateContentStatus(loading, status) {
        // Load shared variables
        var contents = sharedVars.get('contents');
        var selected = sharedVars.get('selected');

        for (var i = 0; i < contents.length; i++) {
            var j = 0;
            while (j < selected.length
                && contents[i].id != selected[j]
            ) {
                j++;
            }

            if (j < selected.length) {
                if (status != null) {
                    contents[i].content_status = status;
                };

                contents[i].loading = loading;
            }
        };

        // Updated shared variable
        sharedVars.set('contents', contents);
        sharedVars.set('selected', selected);
    }

    /**
     * Updates the in_home property for selected contents.
     *
     * @param int loading   Loading flag to use in template.
     * @param int inHome    Available value.
     */
    function updateInHome(loading, inHome) {
        // Load shared variables
        var contents = sharedVars.get('contents');
        var selected = sharedVars.get('selected');

        for (var i = 0; i < contents.length; i++) {
            var j = 0;
            while (j < selected.length
                && contents[i].id != selected[j]
            ) {
                j++;
            }

            if (j < selected.length) {
                if (inHome != null) {
                    contents[i].in_home = inHome;
                };

                contents[i].home_loading = loading;
            }
        };

        // Updated shared variable
        sharedVars.set('contents', contents);
        sharedVars.set('selected', selected);
    }

    /**
     * Updates the selected items.
     *
     * @param  boolean enabled Whether to enable or disable the select.
     * @param  int     id      Selected content id.
     */
    function updateSelected(enabled, id) {
        // Load shared variable
        var selected = sharedVars.get('selected');
        var index    = selected.indexOf(id);

        if (enabled && index === -1) {
          selected.push(id);
        } else {
          selected.splice(index, 1);
        }

        // Updated shared variable
        sharedVars.set('selected', selected);
    };

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

        for (var i = 0; i < contents.length; i++) {
            var j = 0;
            while (j < selected.length
                && contents[i].id != selected[j]
            ) {
                j++;
            }

            if (j < selected.length) {
                if (status != null) {
                    contents[i].status = status;
                };

                contents[i].loading = loading;
            }
        };

        // Updated shared variable
        sharedVars.set('contents', contents);
        sharedVars.set('selected', selected);
    }

    /**
     * Updates the selected items on click.
     *
     * @param  object $event The event.
     * @param  int    id     Selected content id.
     */
    $scope.updateSelection = function($event, id) {
        var checkbox = $event.target;
        var action = checkbox.checked;
        updateSelected(action, id);
    };

    var searchTimeout;

    /**
     * Reloads the list when filters change.
     *
     * @param  object newValues New filters values.
     * @param  object oldValues Old filters values.
     */
    $scope.$watch('shvs.search', function(newValues, oldValues) {
        if (searchTimeout) {
            $timeout.cancel(searchTimeout);
        }

        if (newValues !== oldValues) {
            for (var name in newValues) {
                if (newValues[name] !== oldValues[name]) {
                    $location.search(name, newValues[name]);

                    if (newValues[name] == '') {
                        $location.search(name, null);
                    }
                }
            };

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
    });
}

// Register ContentCtrl function as AngularJS controller
angular.module('BackendApp.controllers').controller('ContentCtrl', ContentCtrl);
