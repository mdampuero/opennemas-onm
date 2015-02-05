/**
 * Controller to implement common actions.
 *
 * @param Object $location    The location service.
 * @param Object $scope       The current scope.
 * @param Object routing The routing service.
 */
angular.module('BackendApp.controllers').controller('MasterCtrl', [
    '$filter', '$http', '$location', '$modal', '$rootScope', '$scope',
    '$translate', '$timeout', '$window', 'paginationConfig',
    function (
        $filter, $http, $location, $modal, $rootScope, $scope, $translate, $timeout,
        $window, paginationConfig
    ) {
        $scope.sidebar = {
            collapsed: false,
            pinned: true,
            forced: false
        };

        /**
         * Flag to show modal window for login only once.
         *
         * @type boolean
         */
        $scope.auth = {
            status: true,
            modal: false,
            inprogress: false
        };

        /**
         * Removes a class from body and checks if user is authenticated.
         */
        $scope.init = function(language) {
            $translate.use(language);

            paginationConfig.nextText     = $filter('translate')('Next');
            paginationConfig.previousText = $filter('translate')('Previous');
        };

        /**
         * Scrolls the page to top.
         */
        $scope.scrollTop = function() {
            $("body").animate({ scrollTop: 0 }, 250);
        };

        /**
         * Updates the content margin-top basing on the filters-navbar height.
         */
        $scope.checkFiltersBar = function checkFiltersBar() {
            $timeout(function() {
                if ($('.view:not(.ng-leave-active) .filters-navbar').length != 1) {
                    return false;
                }

                var margin = 50 + $('.filters-navbar').height() - 15;

                $('.content').css('margin-top', margin + 'px');
            }, 1000);
        };
    }
]);
