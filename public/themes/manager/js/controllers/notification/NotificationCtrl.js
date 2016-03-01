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
         *   The language to edit.
         *
         * @type {String}
         */
        $scope.language = 'en';
        /**
         * @memberOf NotificationCtrl
         *
         * @description
         *   The notification object.
         *
         * @type {Object}
         */
        $scope.notification = {
          body: {
            en: '',
            es: '',
            gl: '',
          },
          instances: [],
          fixed: '0',
          style: 'info',
          title: {
            en: '',
            es: '',
            gl: '',
          },
          type: 'info'
        };

        $scope.languages = {
          'en': 'English',
          'es': 'Spanish',
          'gl': 'Galician',
        };

        /**
         * @memberOf NotificationCtrl
         *
         * @description
         *   The template parameters.
         *
         * @type {Object}
         */
        $scope.extra = data.extra;

        /**
         * @function changeLanguage
         * @memberOf NotificationCtrl
         *
         * @description
         *   Changes the current language.
         *
         * @param {String} lang The language value.
         */
        $scope.changeLanguage = function(lang) {
          $scope.language = lang;
        };

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

          $scope.notification.instances = $scope.notification.instances
            .map(function(a) {
              return a.id;
            });

          if ($scope.notification.start && angular.isObject($scope.notification.start)) {
            $scope.notification.start = $scope.notification.start.toString();
          }

          if ($scope.notification.end && angular.isObject($scope.notification.end)) {
            $scope.notification.end = $scope.notification.end.toString();
          }

          itemService.save('manager_ws_notification_create', $scope.notification)
            .then(function (response) {
              messenger.post({ message: response.data, type: 'success' });

              if (response.status === 201) {
                // Get new notification id
                var url = response.headers()['location'];
                var id  = url.substr(url.lastIndexOf('/') + 1);

                url = routing.ngGenerateShort(
                  'manager_notification_show', { id: id });
                $location.path(url);
              }

              $scope.saving = 0;
            }, function(response) {
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

          var data = angular.copy($scope.notification);

          data.instances = data.instances
            .map(function(a) {
              return a.id;
            });

          itemService.update('manager_ws_notification_update', $scope.notification.id,
            data).success(function (response) {
              messenger.post({ message: response, type: 'success' });
              $scope.saving = 0;
            }).error(function(response) {
              messenger.post({ message: response, type: 'error' });
              $scope.saving = 0;
            });
        };

        $scope.$on('$destroy', function() {
          $scope.notification = null;
        });


        if (data.notification) {
          $scope.notification = data.notification;
        }

        $scope.test = function(query) {
          var tags = [];

          for (var i = 0; i < $scope.extra.instances.length;  i++) {
            var instance = $scope.extra.instances[i];
            if (!query || instance.name.indexOf(query.toLowerCase()) !== -1) {
              tags.push(instance);
            }
          }

          return tags;
        };
      }
    ]);
})();
