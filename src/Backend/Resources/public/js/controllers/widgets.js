// Controller to handle widgets section
angular.module('BackendApp.controllers').controller(
    'WidgetsCtrl', ['$http', '$location', '$modal', '$scope',
    function($http, $location, $modal, $scope) {
        $scope.loading = 1;
        $scope.available = -1;
        $scope.type = -1;

        $scope.filters = {
            elements_per_page: 10,
            page:              1,
            sort_by:           'title',
            sort_order:        'asc',
            search:            { "content_type_name": "widget" }
        }

        // Reloads the list of  widgets
        $scope.list = function(data) {
            $scope.loading  = 1;
            var url = Routing.generate('backend_ws_widgets_list');
            $http.post(url, data).success(function(response) {
                $scope.total    = response.total;
                $scope.page     = response.page;
                $scope.contents = response.results;
                $scope.loading  = 0;
            });
        };

        // Opens new modal window when clicking delete button
        $scope.open = function(index) {
            $modal.open({
                templateUrl: 'modal-delete',
                controller: 'WidgetModalCtrl',
                resolve: {
                    contents: function () {
                        return $scope.contents;
                    },
                    title: function () {
                        return $scope.contents[index].title;
                    },
                    index: function () {
                        return index;
                    },
                    id: function () {
                        return $scope.contents[index].pk_widget;
                    }
                }
            });
        }

        // Goes to widget edit page
        $scope.edit = function(id) {
            var url = Routing.generate('admin_widget_show', { id: id});
            document.location = url;
        }

        // Reloads the list when page changes
        $scope.selectPage = function(page) {
            $scope.filters.page = page;
            $scope.list($scope.filters);
        };

        // Changes widget available property
        $scope.toggleAvailable = function (index, id) {
            var url = Routing.generate('backend_ws_widget_toggle_available', { id: id });
            $scope.contents[index].loading = 1;
            $http.post(url).success(function(response) {
                if (response.status == 'OK') {
                    $scope.contents[index].available = response.available;
                    $scope.contents[index].loading = 0;
                }
            });
        };

        // Reloads the list when type filter changes
        $scope.$watch('type', function(newValue, oldValue) {
            if(newValue === oldValue){
                return;
            }

            if (newValue == -1) {
                delete $scope.filters.search.renderlet;
            } else {
                $scope.filters.search.renderlet = $scope.type;
            }

            $scope.list($scope.filters);
        });


        // Reloads the list when status filter changes
        $scope.$watch('available', function(newValue, oldValue) {
            if(newValue === oldValue){
                return;
            }

            if (newValue == -1) {
                delete $scope.filters.search.available;
            } else {
                $scope.filters.search.available = $scope.available;
            }

            $scope.list($scope.filters);
        });
    }]
);

// Controller to handle modal
angular.module('BackendApp.controllers').controller(
    'WidgetModalCtrl',
    function($http, $scope, $modalInstance, contents, title, index, id) {
        $scope.contents = contents;
        $scope.title = title;
        $scope.index = index;
        $scope.id = id;

        // Closes the current modal
        $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
        };

        // Deletes widget on confirmation
        $scope.delete = function (index, id) {
            var url = Routing.generate('backend_ws_widget_delete', { id: id });
            $http.post(url).success(function(response) {
                if (response.status == 'OK') {
                    $scope.contents.splice(index, 1);
                    $modalInstance.close();
                }
            });
        };
    }
);
