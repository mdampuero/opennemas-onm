(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  NotificationCtrl
     *
     * @requires $http
     * @requires $scope
     * @requires $timeout
     * @requires routing
     *
     * @description
     *   Controller to implement common actions.
     */
    .controller('NotificationCtrl', [ '$http', '$location', '$scope', '$timeout', 'routing', 'oqlEncoder', 'queryManager', '$anchorScroll',
      function ($http, $location, $scope, $timeout, routing, oqlEncoder, queryManager, $anchorScroll) {
        /**
         * The notifications dropdown status.
         *
         * @type Object
         */
        $scope.isOpen = false;

        /**
         * @function list
         * @memberOf NotificationCtrl
         *
         * @description
         *   Gets a list of notifications.
         */
        $scope.list = function() {
          $scope.loading = true;

          var processedFilters = oqlEncoder.encode($scope.criteria);
          var filtersToEncode = angular.copy($scope.criteria);

          var url = routing.generate('backend_ws_notifications_list');

          $http.get(url).success(function(response) {
            $scope.loading = false;
            $scope.notifications = response.results;
            $scope.extra = response.extra;
          });
        };

        // Updates the notification dropdown status
        $scope.$watch(function() {
          return $('.dropdown-notifications').attr('class');
        }, function (nv) {
          $scope.isOpen = false;
          if (nv.indexOf('open') !== -1) {
            $scope.isOpen = true;
          }
        });

        // Get unread notifications
        $scope.$watch('notifications', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          $scope.unread = nv.filter(function(a) {
            return parseInt(a.read) === 0;
          });

          $scope.fixed = nv.filter(function(a) {
            return parseInt(a.fixed) === 1 && parseInt(a.generated) !== 1;
          });
        }, true);

        // Prevent dropdown to close on click
        $('.dropdown-menu .notification-list').click(function(e) {
          e.stopPropagation();
        });

        // Watch location path after rendering list
        $scope.$on('ngRepeatFinished', function () {
          $scope.$watch(function() {
            return $location.path();
          }, function () {
            var id = '#notification-' + $location.path().replace('/', '');

            if ($(id)) {
              $('body').stop().animate({
                scrollTop: $(id).offset().top - 100
              }, '500', 'swing');
            }
          });
        });
      }
    ]);
})();
