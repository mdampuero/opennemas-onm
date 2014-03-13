
angular.module('BackendApp.controllers')
    .controller('ArticlesController', ['$attrs', '$http', '$scope', function($attrs, $http, $scope) {

        $scope.data = {
            elements_per_page: 10,
            page:              1,
            search:            '',
            sort_by:           'title',
            sort_order:        'asc',
            search:            {"content_type_name": "article"}
        }

        $scope.list = function(data) {
            $http.post($attrs.url, data).success(function(response) {
                $scope.total    = response.total;
                $scope.page     = response.page;
                $scope.contents = response.results;

                console.log($scope.total);
                console.log($scope.page);
                console.log($scope.contents);
            });
        };

        $scope.selectPage = function(page) {
            console.log(page);
            $scope.data.page = page;
            $scope.list($scope.data);
        };
}]);
