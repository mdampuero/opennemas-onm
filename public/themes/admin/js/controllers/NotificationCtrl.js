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
    .controller('NotificationCtrl', [ '$http', '$scope', '$timeout', 'routing',
      function ($http, $scope, $timeout, routing) {
        /**
         * @function getLatests
         * @memberOf NotificationCtrl
         *
         * @description
         *   Gets a list of notifications to display in dropdown.
         */
        $scope.getLatests = function() {
          var data = {
            epp: 10,
            page: 1,
            criteria: {
              is_read: [ { value: 0 } ]
            }
          };

          var url = routing.generate('backend_ws_notifications_list', data);

          $http.get(url).success(function(response) {
            $scope.notifications = response.results;

            $scope.bounce = true;
            $timeout(function() { $scope.bounce = false; }, 1000);
          });
        };

        /**
         * @function markAsRead
         * @memberOf NotificationCtrl
         *
         * @description
         *   Marks a notification as read.
         *
         * @param {Integer} index The index of the notification to mark.
         */
        $scope.markAsRead = function(index) {
          var notification = $scope.notifications[index];

          var url = routing.generate('backend_ws_notification_patch',
              { id: notification.id });

          $http.patch(url).success(function() {
            $scope.notifications.splice(index, 1);
            $scope.pulse = true;
            $timeout(function() { $scope.pulse = false; }, 1000);
          });
        };

        // List all notifications on init
        $scope.getLatests();

        // Prevent dropdown to close on click
        $('.dropdown-menu .notification-list').click(function(e) {
          e.stopPropagation();
        });
      }
    ]);
})();

