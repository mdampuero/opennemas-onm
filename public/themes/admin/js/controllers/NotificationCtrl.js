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
    .controller('NotificationCtrl', [ '$http', '$scope', '$timeout', 'routing', 'oqlEncoder', 'queryManager',
      function ($http, $scope, $timeout, routing, oqlEncoder, queryManager) {
        /**
         * The criteria to search.
         *
         * @type Object
         */
        $scope.criteria = { type: '' };

        /**
         * The current pagination status.
         *
         * @type Object
         */
        $scope.pagination = {
          epp: 10,
          page: 1,
          total: 0
        };

        /**
         * @function getLatest
         * @memberOf NotificationCtrl
         *
         * @description
         *   Gets a list of notifications to display in dropdown.
         */
        $scope.getLatest = function() {
          var data = {
            epp: 10,
            page: 1,
            search: {
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
         * @function getLatest
         * @memberOf NotificationCtrl
         *
         * @description
         *   Gets a list of notifications to display in dropdown.
         */
        $scope.list = function() {
          $scope.loading = true;

          var processedFilters = oqlEncoder.encode($scope.criteria);
          var filtersToEncode = angular.copy($scope.criteria);

          queryManager.setParams(filtersToEncode, {}, 100,
              $scope.pagination.page);

          var data = {
            epp: 100,
            page: $scope.pagination.page,
            search: processedFilters
          };

          var url = routing.generate('backend_ws_notifications_list', data);

          $http.get(url).success(function(response) {
            $scope.loading = false;
            $scope.notifications = response.results;
            $scope.extra = response.extra;
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

        var search;
        $scope.$watch('criteria', function(nv, ov) {
          if (ov === nv) {
            return;
          }

          search = $timeout(function() {
            if (search) {
              $timeout.cancel(search);
            }

            $scope.list();
          },500);
        }, true);

        // Prevent dropdown to close on click
        $('.dropdown-menu .notification-list').click(function(e) {
          e.stopPropagation();
        });
      }
    ]);
})();

