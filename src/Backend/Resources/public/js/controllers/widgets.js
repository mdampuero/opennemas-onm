
angular.module('BackendApp.controllers')
    .controller('WidgetsController', ['$attrs', '$http', '$scope', '$timeout', function($attrs, $http, $scope, $timeout) {

        $scope.available = -1;

        $scope.filters = {
            elements_per_page: 10,
            page:              1,
            sort_by:           'title',
            sort_order:        'asc',
            search:            { "content_type_name": "widget" }
        }

        $scope.list = function(data) {
            $http.post($attrs.url, data).success(function(response) {
                $scope.total    = response.total;
                $scope.page     = response.page;
                $scope.contents = response.results;
            });
        };

        $scope.selectPage = function(page) {
            $scope.filters.page = page;
            $scope.list($scope.filters);
        };

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
}]);
