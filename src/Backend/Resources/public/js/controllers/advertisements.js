
angular.module('BackendApp.controllers')
    .controller('AdvertisementsController', ['$attrs', '$http', '$scope', function($attr, $http, $scope) {

        $scope.status = -1;

        $scope.filters = {
            elements_per_page: 10,
            page:              1,
            sort_by:           'title',
            sort_order:        'asc',
            search:            {"content_type_name": "advertisement", "available": "1"},
            type:              -1
        }

        $scope.list = function(data) {
            $http.post($attr.url, data).success(function(response) {
                $scope.total    = response.total;
                $scope.page     = response.page;
                $scope.contents = response.results;
                $scope.map      = response.map;
            });
        }

        $scope.selectPage = function(page) {
            $scope.filters.page = page;
            $scope.list($scope.filters);
        };

        $scope.$watch('status', function(newValue, oldValue) {
            if(newValue === oldValue){
                return;
            }

            if (newValue == -1) {
                delete $scope.filters.search.available;
            } else {
                $scope.filters.search.available = $scope.status;
            }

            $scope.list($scope.filters);
        });

        $scope.$watch('type', function() {
            $scope.filters.type = $scope.type;
            $scope.list($scope.filters);
        });
}]);
