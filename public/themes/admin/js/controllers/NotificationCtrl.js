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
         * The notifications dropdown status.
         *
         * @type Object
         */
        $scope.isOpen = false;

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

        // Updates the notification dropdown status
        $scope.$watch(function() {
          return $('.dropdown-notifications').attr('class');
        }, function(nv, ov){
          $scope.isOpen = false;
          if (nv.indexOf('open') !== -1) {
            $scope.isOpen = true;
          }
        });

        var search;
        // Reloads the notification list when criteria changes
        $scope.$watch('criteria', function(nv, ov) {
          if (ov === nv) {
            return;
          }

          search = $timeout(function() {

            if (search) {
              $timeout.cancel(search);
            }

            $scope.list();
          }, 500);
        }, true);

        // Get unread notifications
        $scope.$watch('notifications', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          $scope.unread = nv.filter(function(a) {
            return a.read == 0;
          });

          $scope.fixed = nv.filter(function(a) {
            return a.fixed == 1 && a.generated != 1;
          });
        }, true);

        // Prevent dropdown to close on click
        $('.dropdown-menu .notification-list').click(function(e) {
          e.stopPropagation();
        });
      }
    ]);
})();
