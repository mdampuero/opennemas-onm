
angular.module('BackendApp.controllers')
    .controller('MenusController', ['$attrs', '$http', '$scope', function($attrs, $http, $scope) {

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
            $http.post($attrs.url, data).success(function(response) {
                $scope.total    = response.total;
                $scope.page     = response.page;
                $scope.contents = response.results;
                $scope.loading  = 0;

            });
        };

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

        $scope.selectPage = function (page) {
            $scope.filters.page = page;
            $scope.list($scope.filters);
        };

        // Deletes a menu
        $scope.delete = function (index, url) {
            $http.post(url).success(function(response) {
                if (response.status == 'OK') {
                    delete $scope.contents[index];
                }
            });
        }
}]);
