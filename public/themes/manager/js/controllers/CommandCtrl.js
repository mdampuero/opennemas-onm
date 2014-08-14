

angular.module('ManagerApp.controllers').controller('CommandCtrl',
    function ($scope, itemService, list) {

        $scope.commands = list.commands;
        $scope.instances = list.instances;
    }
);
