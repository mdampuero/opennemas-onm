// Controller to handle menus section
angular.module('BackendApp.controllers').controller(
    'MenusCtrl', ['$http', '$location', '$modal', '$scope',
    function($http, $location, $modal, $scope) {
        $scope.loading = 1;

        $scope.all_selected = false;
        $scope.selected = [];

        $scope.filters = {
            elements_per_page: 10,
            page:              1,
            sort_by:           'name',
            sort_order:        'asc',
            search:            {}
        }

        $scope.list = function(data) {
            $scope.loading  = 1;
            var url = Routing.generate('backend_ws_menus_list');
            $http.post(url, data).success(function(response) {
                $scope.total    = response.total;
                $scope.page     = response.page;
                $scope.contents = response.results;
                $scope.loading  = 0;
            });
        };

        // Opens new modal window when clicking delete button
        $scope.open = function(tpl, index) {
            $modal.open({
                templateUrl: tpl,
                controller: 'MenuModalCtrl',
                resolve: {
                    contents: function () {
                        return $scope.contents;
                    },
                    index: function () {
                        return index;
                    },
                    id: function () {
                        if (index) {
                            return $scope.contents[index].pk_menu;
                        }

                        return null;
                    },
                    name: function () {
                        if (index) {
                            return $scope.contents[index].name;
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
                for (var menu in $scope.contents) {
                    updateSelected($scope.all_selected, $scope.contents[menu].pk_menu)
                };
            }
        };

        // Goes to menu edit page
        $scope.edit = function(id) {
            var url = Routing.generate('admin_menu_show', { id: id});
            document.location = url;
        }

        // Reloads the list when page changes
        $scope.selectPage = function (page) {
            $scope.filters.page = page;
            $scope.list($scope.filters);
        };
    }]
);

// Controller to handle modal
angular.module('BackendApp.controllers').controller(
    'MenuModalCtrl',
    function($http, $scope, $modalInstance, contents, selected, name, index, id) {
        $scope.contents = contents;
        $scope.selected = selected;
        $scope.name = name;
        $scope.index = index;
        $scope.id = id;

        // Closes the current modal
        $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
        };

        // Deletes widget on confirmation
        $scope.delete = function (index, id) {
            var url = Routing.generate('backend_ws_menu_delete', { id: id });
            $http.post(url).success(function(response) {
                if (response.status == 'OK') {
                    $scope.contents.splice(index, 1);
                    $modalInstance.close();
                }
            });
        };

        // Deletes selected widgets on confirmation
        $scope.deleteSelected = function () {
            var url = Routing.generate('backend_ws_menus_batch_delete');
            $http.post(url, { ids: $scope.selected }).success(function(response) {
                if (response.status == 'OK') {
                    for (var i = 0; i < $scope.contents.length; i++) {
                        var j = 0;
                        while (j < $scope.selected.length
                            && $scope.contents[i].pk_menu != $scope.selected[j]
                        ) {
                            j++;
                        }

                        if (j < $scope.selected.length) {
                            $scope.contents.splice(i, 1);
                        }
                    };
                    $modalInstance.close();
                }
            });
        };
    }
);
