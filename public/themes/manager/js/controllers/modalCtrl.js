
angular.module('ManagerApp.controllers').controller('modalCtrl', [
    '$modalInstance', '$scope', 'template', 'success',
    function ($modalInstance, $scope, template, success) {
        $scope.template = template;

        /**
         * Closes the current modal
         */
        $scope.close = function() {
            $modalInstance.close(false);
        };

        /**
         * Confirms and executes the confirmed action.
         */
        $scope.confirm = function() {
            $scope.loading = 1;

            success().then(function (response) {
                $scope.loading = 0
                $modalInstance.close(response);
            });
        }

        /**
         * Frees up memory before controller destroy event
         */
        $scope.$on('$destroy', function() {
            $scope.template = null;
        })
    }
]);
