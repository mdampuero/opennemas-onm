

angular.module('ManagerApp.controllers').controller('InstanceCtrl',
    function ($scope, itemService, list, fosJsRouting) {



        $scope.page  = 1;
        $scope.epp   = 25;
        $scope.items = list.results;
        $scope.total = list.total;


    }
);
