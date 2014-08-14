

angular.module('ManagerApp.controllers').controller('InstanceCtrl',
    function ($scope, itemService, list) {

        $scope.page = 1;
        $scope.total = 0;


        $scope.items = list.results;
        $scope.total = list.total;
    }
);
