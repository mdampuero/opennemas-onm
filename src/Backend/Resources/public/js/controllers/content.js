/**
 * Controller to handle list actions.
 */
function ContentCtrl($http, $location, $modal, $scope, $timeout, fosJsRouting) {
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
    $scope.filters = {
        elements_per_page: 10,
        page:              1,
        sort_by:           'title',
        sort_order:        'asc',
        search: {
            content_type_name:     -1,
            available:             -1,
            category_name:         -1,
            with_script:           -1,
        },
    }

    /**
     * Array of selected items.
     *
     * @type array
     */
    $scope.selected = [];

    /**
     * Returns if the given content is selected.
     *
     * @return boolean True if the given content is selected. Otherwise, return
     *                 false.
     */
    $scope.areSelected = function() {
        var selected = $scope.selected ? $scope.selected.length : 0;
        var contents = $scope.contents ? $scope.contents.length : 0;

        return selected == contents;
    };

    /**
     * Changes the available property for selected contents.
     *
     * @param int    available Available value.
     * @param string route     Route name.
     */
    $scope.batchToggleAvailable = function (available, route) {
        updateAvailable(1);

        var url = fosJsRouting.generate(route, { contentType: $scope.contentType });
        $http.post(url, { ids: $scope.selected, available: available })
            .success(function(response) {
                updateAvailable(0, available);
            });
    };

    /**
     * Changes the available property for selected contents.
     *
     * @param int    available Available value.
     * @param string route     Route name.
     */
    $scope.batchToggleStatus = function (status, route) {
        updateStatus(1);

        var url = fosJsRouting.generate(route, { contentType: $scope.contentType });
        $http.post(url, { ids: $scope.selected, status: status })
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
        updateInHome(1);

        var url = fosJsRouting.generate(route, { contentType: $scope.contentType });
        $http.post(url, { ids: $scope.selected, in_home: inHome })
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
            if (filters[name] != -1 && filters[name] != '') {
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
        var url = fosJsRouting.generate(route, { contentType: $scope.contentType, id: id});
        document.location = url;
    }

    /**
     * Initializes the content type for the current list.
     *
     * @param string content Content type.
     * @param string route   Route name.
     */
    $scope.init = function(content, filters, sortBy, route) {
        $scope.contentType = content;

        // Initialize filters
        for (var filter in filters) {
            $scope.filters.search[filter] = filters[filter];
        };

        // Load filters from URL
        var search = $location.search();
        for (var filter in search) {
            if (filters[filter] != null) {
                $scope.filters.search[filter] = search[filter];
            }
        };

        // Route for list (required by $watch)
        $scope.route = route;

        if (content != null) {
            $scope.filters.search.content_type_name = content;
        }

        if (sortBy != null) {
            $scope.filters.sort_by = sortBy;
        }

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
        return $scope.selected.indexOf(id) >= 0;
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
            elements_per_page: $scope.filters.elements_per_page,
            page:              $scope.filters.page,
            sort_by:           $scope.filters.sort_by,
            sort_order:        $scope.filters.sort_order,
            search:            clearFilters($scope.filters.search)
        }

        var url = fosJsRouting.generate(route, { contentType: $scope.contentType });
        $http.post(url, postData).success(function(response) {
            $scope.total    = response.total;
            $scope.page     = response.page;
            $scope.contents = response.results;
            $scope.map      = response.map;

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
                contentType: function () {
                    return $scope.contentType;
                },
                contents: function () {
                    return $scope.contents;
                },
                index: function () {
                    return index;
                },
                id: function () {
                    if (index != null) {
                        return $scope.contents[index].id;
                    }

                    return null;
                },
                route: function () {
                    return route;
                },
                selected: function () {
                    return $scope.selected;
                },
                title: function () {
                    if (index != null) {
                        if ($scope.contents[index].title) {
                            return $scope.contents[index].title;
                        } else if ($scope.contents[index].name) {
                            return $scope.contents[index].name;
                        } else {
                            return $scope.contents[index].id;
                        }
                    }

                    return null;
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
        $scope.selected = [];

        if ($scope.allSelected) {
            for (var content in $scope.contents) {
                updateSelected($scope.allSelected, $scope.contents[content].id);
            };
        }
    };

    /**
     * Changes the page of the list.
     *
     * @param  int page Page number.
     */
    $scope.selectPage = function(page, route) {
        if (page != $scope.filters.page) {
            $scope.filters.page = page;
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
    $scope.toggleAvailable = function (id, index, route) {
        // Enable spinner
        $scope.contents[index].loading = 1;

        var url = fosJsRouting.generate(route, { contentType: $scope.contentType, id: id });
        $http.post(url).success(function(response) {
            if (response.available != null) {
                $scope.contents[index].available = response.available;
            }

            // Disable spinner
            $scope.contents[index].loading = 0;
        }).error(function(response) {
            // Disable spinner
            $scope.contents[index].loading = 0;
        });
    };

    /**
     * Changes the content in_home property.
     *
     * @param int    id    Content id.
     * @param int    index Index of content in the array of contents.
     * @param string route Route name.
     */
    $scope.toggleInHome = function (id, index, route) {
        // Enable spinner
        $scope.contents[index].home_loading = 1;

        var url = fosJsRouting.generate(route, { contentType: $scope.contentType, id: id });
        $http.post(url).success(function(response) {
            if (response.in_home != null) {
                $scope.contents[index].in_home = response.in_home;
            }

            // Disable spinner
            $scope.contents[index].home_loading = 0;
        }).error(function(response) {
            // Disable spinner
            $scope.contents[index].home_loading = 0;
        });
    };

    /**
     * Changes the content favorite property.
     *
     * @param int    id    Content id.
     * @param int    index Index of content in the array of contents.
     * @param string route Route name.
     */
    $scope.toggleFavorite = function (id, index, route) {
        // Enable spinner
        $scope.contents[index].favorite_loading = 1;

        var url = fosJsRouting.generate(route, { contentType: $scope.contentType, id: id });
        $http.post(url).success(function(response) {
            if (response.favorite != null) {
                $scope.contents[index].favorite = response.favorite;
            }

            // Disable spinner
            $scope.contents[index].favorite_loading = 0;
        }).error(function(response) {
            // Disable spinner
            $scope.contents[index].favorite_loading = 0;
        });
    };

    /**
     * Changes the content available property.
     *
     * @param int    id    Content id.
     * @param int    index Index of content in the array of contents.
     * @param string route Route name.
     */
    $scope.toggleStatus = function (id, index, route) {
        // Enable spinner
        $scope.contents[index].loading = 1;

        var url = fosJsRouting.generate(route, { contentType: $scope.contentType, id: id });
        $http.post(url).success(function(response) {
            if (response.status != null) {
                $scope.contents[index].status = response.status;
                console.log($scope.contents[index].status);
            }

            // Disable spinner
            $scope.contents[index].loading = 0;
        }).error(function(response) {
            // Disable spinner
            $scope.contents[index].loading = 0;
        });
    };

    /**
     * Updates the available property for selected contents.
     *
     * @param int loading   Loading flag to use in template.
     * @param int available Available value.
     */
    function updateAvailable(loading, available) {
        for (var i = 0; i < $scope.contents.length; i++) {
            var j = 0;
            while (j < $scope.selected.length
                && $scope.contents[i].id != $scope.selected[j]
            ) {
                j++;
            }

            if (j < $scope.selected.length) {
                if (available != null) {
                    $scope.contents[i].available = available;
                };

                $scope.contents[i].loading = loading;
            }
        };
    }

    /**
     * Updates the in_home property for selected contents.
     *
     * @param int loading   Loading flag to use in template.
     * @param int inHome    Available value.
     */
    function updateInHome(loading, inHome) {
        for (var i = 0; i < $scope.contents.length; i++) {
            var j = 0;
            while (j < $scope.selected.length
                && $scope.contents[i].id != $scope.selected[j]
            ) {
                j++;
            }

            if (j < $scope.selected.length) {
                if (inHome != null) {
                    $scope.contents[i].in_home = inHome;
                };

                $scope.contents[i].home_loading = loading;
            }
        };
    }

    /**
     * Updates the selected items.
     *
     * @param  boolean enabled Whether to enable or disable the select.
     * @param  int     id      Selected content id.
     */
    function updateSelected(enabled, id) {
        var index = $scope.selected.indexOf(id);

        if (enabled && index === -1) {
          $scope.selected.push(id);
        } else {
          $scope.selected.splice(index, 1);
        }
    };

    /**
     * Updates the status property for selected contents.
     *
     * @param int loading Loading flag to use in template.
     * @param int status  Status value.
     */
    function updateStatus(loading, status) {
        for (var i = 0; i < $scope.contents.length; i++) {
            var j = 0;
            while (j < $scope.selected.length
                && $scope.contents[i].id != $scope.selected[j]
            ) {
                j++;
            }

            if (j < $scope.selected.length) {
                if (status != null) {
                    $scope.contents[i].status = status;
                };

                $scope.contents[i].loading = loading;
            }
        };
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
    $scope.$watch('filters.search', function(newValues, oldValues) {
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
}

// Register ContentCtrl function as AngularJS controller
angular.module('BackendApp.controllers').controller('ContentCtrl', ContentCtrl);
//
