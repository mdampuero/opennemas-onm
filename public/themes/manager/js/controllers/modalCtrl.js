
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

            var getType = {};
            if (success && getType.toString.call(success) === '[object Function]') {
                success().then(function (response) {
                    $scope.loading = 0
                });
            } else {
                $modalInstance.close(true);
            }
        }

        /**
         * Frees up memory before controller destroy event
         */
        $scope.$on('$destroy', function() {
            $scope.template = null;
        })
    }
]);
