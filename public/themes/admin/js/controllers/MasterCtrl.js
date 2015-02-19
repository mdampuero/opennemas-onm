/**
 * Controller to implement common actions.
 *
 * @param Object $location    The location service.
 * @param Object $scope       The current scope.
 * @param Object routing The routing service.
 */
angular.module('BackendApp.controllers').controller('MasterCtrl', [
    '$filter', '$http', '$location', '$modal', '$rootScope', '$scope',
    '$translate', '$timeout', '$window', 'paginationConfig', 'renderer',
    'routing',
    function (
        $filter, $http, $location, $modal, $rootScope, $scope, $translate, $timeout,
        $window, paginationConfig, renderer, routing
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
         *
         * @param string  language The current language.
         * @param boolean pinned   The current sidebar pinned status.
         */
        $scope.init = function(language, pinned) {
            $translate.use(language);

            paginationConfig.nextText     = $filter('translate')('Next');
            paginationConfig.previousText = $filter('translate')('Previous');

            $scope.sidebar.pinned = pinned;
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

        /**
         * Updates the sidebar status basing on the current window width.
         *
         * @param integer nv The new value.
         * @param integer ov The old value.
         */
        $scope.$watch('windowWidth', function(nv, ov) {
          if (nv <= 1024) {
            $scope.sidebar.forced = true;
            $scope.sidebar.collapsed = true;
          } else {
            $scope.sidebar.forced = false;
            $scope.sidebar.collapsed = !$scope.sidebar.pinned;
          }
        });

        /**
         * Sends a request to update the sidebar pinned status in server.
         *
         * @param integer nv The new value.
         * @param integer ov The old value.
         */
        $scope.$watch('sidebar.pinned', function(nv, ov) {
          if (nv == ov) {
            return;
          }

          $http.put(routing.generate('admin_menu_sidebar_set'), { pinned: nv});
        });
    }
]);
