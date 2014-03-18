
angular.module('BackendApp.controllers').controller('AdvertisementsController',
    ['$http', '$location', '$modal', '$scope',
    function($http, $location, $modal, $scope) {
        $scope.filters = {
            elements_per_page: 10,
            page:              1,
            sort_by:           'title',
            sort_order:        'asc',
            search: {
                fk_content_categories: -1,
                content_type_name:     "advertisement",
                available:             -1,
                type_advertisement:    -1,
                with_script:           -1,
            },
        }

        $scope.all_selected = false;
        $scope.selected = [];

        /**
         * Removes invalid filters from array of filters.
         *
         * @param  Object $filters Filters.
         * @return Object          Filters after removing invalid properties.
         */
        var cleanFilters = function clearFilters(filters) {
            var cleaned = {};
            for (var name in filters) {
                if (filters[name] != -1) {
                    cleaned[name] = filters[name];
                }
            };

            return cleaned;
        }

        // Load category from URL
        if ($location.search().category && $location.search().category != -1) {
            $scope.filters.search.fk_content_categories = $location.search().category;
        }

        // Load status from URL
        if ($location.search().status && $location.search().status != -1) {
            $scope.filters.search.available = $location.search().status;
        }

        // Load type from URL
        if ($location.search().type && $location.search().type != -1) {
            $scope.filters.search.type_advertisement = $location.search().type;
        }

        // Load with from URL
        if ($location.search().with && $location.search().with != -1) {
            $scope.filters.search.with_script = $location.search().with;
        }

        $scope.list = function(data) {
            $scope.loading = 1;

            var postData = {
                elements_per_page: data.elements_per_page,
                page:              data.page,
                sort_by:           data.sort_by,
                sort_order:        data.sort_order,
                search:            cleanFilters(data.search)
            }

            var url = Routing.generate('backend_ws_advertisements_list');
            $http.post(url, postData).success(function(response) {
                $scope.total    = response.total;
                $scope.page     = response.page;
                $scope.contents = response.results;
                $scope.map      = response.map;

                $scope.loading = 0;
            });
        }

        // Opens new modal window when clicking delete button
        $scope.open = function(tpl, index) {
            $modal.open({
                templateUrl: tpl,
                controller: 'AdvertisementModalCtrl',
                resolve: {
                    contents: function () {
                        return $scope.contents;
                    },
                    index: function () {
                        return index;
                    },
                    id: function () {
                        if (index != null) {
                            return $scope.contents[index].pk_advertisement;
                        }

                        return null;
                    },
                    title: function () {
                        if (index != null) {
                            return $scope.contents[index].title;
                        }

                        return null;
                    },
                    selected: function () {
                        return $scope.selected;
                    }
                }
            });
        }

        var updateSelected = function (select, id) {
            var index = $scope.selected.indexOf(id);

            if (select && index === -1) {
              $scope.selected.push(id);
            } else {
              $scope.selected.splice(index, 1);
            }
        };

        $scope.updateSelection = function($event, id) {
            var checkbox = $event.target;
            var action = checkbox.checked;
            updateSelected(action, id);
        };

        $scope.isSelected = function(id) {
            return $scope.selected.indexOf(id) >= 0;
        };

        $scope.selectAll = function ($event) {
            var checkbox = $event.target;

            $scope.all_selected = !$scope.all_selected;
            $scope.selected = [];

            if ($scope.all_selected) {
                for (var ad in $scope.contents) {
                    updateSelected($scope.all_selected, $scope.contents[ad].id)
                };
            }
        };

        // Goes to widget edit page
        $scope.edit = function(id) {
            var url = Routing.generate('admin_ad_show', { id: id});
            document.location = url;
        }

        $scope.selectPage = function(page) {
            if (page != $scope.filters.page) {
                $scope.filters.page = page;
                $scope.list($scope.filters);
            }
        };

        // Changes widget available property
        $scope.toggleAvailable = function (index, id) {
            var url = Routing.generate('backend_ws_advertisement_toggle_available', { id: id });
            $scope.contents[index].loading = 1;
            $http.post(url).success(function(response) {
                if (response.status == 'OK') {
                    $scope.contents[index].available = response.available;
                    $scope.contents[index].loading = 0;
                }
            });
        };

        // Changes widget available property
        $scope.batchToggleAvailable = function (available) {
            for (var i = 0; i < $scope.contents.length; i++) {
                var j = 0;
                while (j < $scope.selected.length
                    && $scope.contents[i].id != $scope.selected[j]
                ) {
                    j++;
                }

                if (j < $scope.selected.length) {
                    $scope.contents[i].loading = 1;
                }
            };

            var url = Routing.generate('backend_ws_advertisements_batch_toggle_available');
            $http.post(url, { ids: $scope.selected, available: available }).success(function(response) {
                if (response.status == 'OK') {
                    for (var i = 0; i < $scope.contents.length; i++) {
                        var j = 0;
                        while (j < $scope.selected.length
                            && $scope.contents[i].id != $scope.selected[j]
                        ) {
                            j++;
                        }

                        if (j < $scope.selected.length) {
                            $scope.contents[i].available = available;
                            $scope.contents[i].loading = 0;
                        }
                    };
                    $modalInstance.close();
                }

                $scope.deleting = 0;
            });
        };

        $scope.$watch('filters.search', function(newValues, oldValues) {
            if (newValues !== oldValues) {
                for (var name in newValues) {
                    if (newValues[name] !== oldValues[name]) {
                        $location.search(name, newValues[name]);
                    }
                };

                $scope.list($scope.filters);
            }
        }, true);
    }
]);

// Controller to handle modal
angular.module('BackendApp.controllers').controller(
    'AdvertisementModalCtrl',
    function($http, $scope, $modalInstance, contents, selected, title, index, id) {
        $scope.contents = contents;
        $scope.selected = selected;
        $scope.title = title;
        $scope.index = index;
        $scope.id = id;

        // Closes the current modal
        $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
        };

        // Deletes widget on confirmation
        $scope.delete = function (index, id) {
            $scope.deleting = 1;

            var url = Routing.generate('backend_ws_advertisement_delete', { id: id });
            $http.post(url).success(function(response) {
                if (response.status == 'OK') {
                    $scope.contents.splice(index, 1);
                    $modalInstance.close();
                }

                $scope.deleting = 0;
            });

        };

        // Deletes selected widgets on confirmation
        $scope.deleteSelected = function () {
            $scope.deleting = 1;

            var url = Routing.generate('backend_ws_advertisements_batch_delete');
            $http.post(url, { ids: $scope.selected }).success(function(response) {
                if (response.status == 'OK') {
                    for (var i = 0; i < $scope.contents.length; i++) {
                        var j = 0;
                        while (j < $scope.selected.length
                            && $scope.contents[i].id != $scope.selected[j]
                        ) {
                            j++;
                        }

                        if (j < $scope.selected.length) {
                            $scope.contents.splice(i, 1);
                        }
                    };
                    $modalInstance.close();
                }

                $scope.deleting = 0;
            });
        };
    }
);
