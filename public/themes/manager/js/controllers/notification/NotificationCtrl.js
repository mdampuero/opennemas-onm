(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  NotificationCtrl
     *
     * @requires $filter
     * @requires $location
     * @requires $modal
     * @requires $scope
     * @requires itemService
     * @requires routing
     * @requires messenger
     * @requires data
     *
     * @description
     *   Handles actions for notification edition form
     */
    .controller('NotificationCtrl', [
      '$filter', '$location', '$modal', '$scope', 'itemService', 'routing', 'messenger', 'data',
      function ($filter, $location, $modal, $scope, itemService, routing, messenger, data) {
        /**
         * @memberOf NotificationCtrl
         *
         * @description
         *   The notification object.
         *
         * @type {Object}
         */
        $scope.notification = data.notification;

        /**
         * @memberOf NotificationCtrl
         *
         * @description
         *   The template parameters.
         *
         * @type {Object}
         */
        $scope.extra = data.extra;

        console.log($scope.notification);

        /**
         * @function save
         * @memberOf NotificationCtrl
         *
         * @description
         *   Creates a new notification.
         */
        $scope.save = function() {
          if ($scope.notificationForm.$invalid) {
            $scope.formValidated = 1;

            messenger.post({
              message: $filter('translate')('FormErrors'),
              type:    'error'
            });

            return false;
          }

          $scope.saving = 1;

          if ($scope.notification.domain_expire && angular.isObject($scope.instance.domain_expire)) {
            $scope.notification.domain_expire = $scope.instance.domain_expire.toString();
          }

          if ($scope.notification.external.last_invoice && angular.isObject($scope.instance.external.last_invoice)) {
            $scope.notification.external.last_invoice = $scope.instance.external.last_invoice.toString();
          }

          itemService.save('manager_ws_notification_create', $scope.instance)
            .success(function (response) {
              messenger.post({ message: response, type: 'success' });

              if (response.status === 201) {
                // Get new notification id
                var url = response.headers()['location'];
                var id  = url.substr(url.lastIndexOf('/') + 1);

                url = routing.ngGenerateShort(
                  'manager_notification_show', { id: id });
                $location.path(url);
              }

              $scope.saving = 0;
            }).error(function(response) {
              $scope.saving = 0;
              messenger.post({ message: response, type: 'error' });
            });
        };

         /**
         * @function update
         * @memberOf NotificationCtrl
         *
         * @description
         *   Updates an notification.
         */
        $scope.update = function() {
          if ($scope.notificationForm.$invalid) {
            $scope.formValidated = 1;

            messenger.post({
              message: $filter('translate')('FormErrors'),
              type:    'error'
            });

            return false;
          }

          $scope.saving = 1;

          if ($scope.notification.end && angular.isObject($scope.notification.end)) {
            $scope.notification.end = $scope.notification.end.toString();
          }

          if ($scope.notification.start && angular.isObject($scope.notification.start)) {
            $scope.notification.start = $scope.notification.start.toString();
          }

          itemService.update('manager_ws_notification_update', $scope.notification.id,
            $scope.notification).success(function (response) {
              messenger.post({ message: response, type: 'success' });
              $scope.saving = 0;
            }).error(function(response) {
              messenger.post({ message: response, type: 'error' });
              $scope.saving = 0;
            });
          };

          $scope.$on('$destroy', function() {
            $scope.notification = null;
            $scope.changed_modules = null;
            $scope.template = null;
            $scope.selected = null;
          });
      }
    ]);
})();
