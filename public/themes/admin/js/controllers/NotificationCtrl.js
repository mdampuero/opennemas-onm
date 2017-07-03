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
    .controller('NotificationCtrl', [ '$http', '$location', '$scope', '$timeout', '$window', 'routing',
      function ($http, $location, $scope, $timeout, $window, routing) {
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

          var url = routing.generate('backend_ws_notifications_list');

          $http.get(url).success(function(response) {
            $scope.loading       = false;
            $scope.notifications = response.results;
            $scope.extra         = response.extra;

            $scope.markAllAsOpen();
          });
        };

        /**
         * @function markAllAsOpen
         * @memberOf NotificationCtrl
         *
         * @description
         *   Marks all notifications as open.
         */
        $scope.markAllAsOpen = function() {
          var ids  = $scope.notifications.filter(function(e) {
            return angular.isNumber(e.id);
          }).map(function(e) { return e.id; });

          if (ids.length === 0) {
            return;
          }

          var url  = routing.generate('backend_ws_notifications_patch');
          var date = new Date();

          var data = {
            ids: ids,
            'open_date': $window.moment(date).format('YYYY-MM-DD HH:mm:ss'),
            'view_date': $window.moment(date).format('YYYY-MM-DD HH:mm:ss')
          };

          $http.patch(url, data);

          // Mark non-fixed as read
          ids = $scope.notifications.filter(function(e) {
            return angular.isNumber(e.id) && !e.fixed;
          }).map(function(e) { return e.id; });

          if (ids.length === 0) {
            return;
          }

          data = {
            ids: ids,
            'read_date': $window.moment(date).format('YYYY-MM-DD HH:mm:ss'),
          };

          $http.patch(url, data);
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

          $scope.notViewed = nv.filter(function(a) {
            return parseInt(a.view) === 0;
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
