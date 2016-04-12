(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  NotificationCtrl
     *
     * @requires $location
     * @requires $scope
     * @requires http
     * @requires messenger
     *
     * @description
     *   Handles actions for notification edition form
     */
    .controller('NotificationCtrl', [
      '$location', '$scope', 'http', 'routing', 'messenger',
      function ($location, $scope, http, routing, messenger) {
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
            gl: ''
          },
          instances: [],
          fixed: '0',
          style: {},
          title: {
            en: '',
            es: '',
            gl: ''
          }
        };

        $scope.languages = {
          en: 'English',
          es: 'Spanish',
          gl: 'Galician'
        };

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
         * @function countStringsLeft
         * @memberOf NotificationCtrl
         *
         * @description
         *   Counts the number of remaining strings for a language.
         *
         * @param {String} lang The language to check.
         *
         * @return {Integer} The number of remaining strings.
         */
        $scope.countStringsLeft = function(lang) {
          var left = 0;

          if (!$scope.notification.title || !$scope.notification.title[lang]) {
            left++;
          }

          if (!$scope.notification.body || !$scope.notification.body[lang]) {
            left++;
          }

          return left;
        };

        /**
         * @function getInstances
         * @memberOf NotificationCtrl
         *
         * @description
         *   Returns a list of instances by query.
         *
         * @param {String} query The instance internal name.
         *
         * @return {Array} The list of instances
         */
        $scope.getInstances = function(query) {
          var tags = [];

          for (var i = 0; i < $scope.extra.instances.length; i++) {
            var instance = $scope.extra.instances[i];
            if (!query || instance.name.indexOf(query.toLowerCase()) !== -1) {
              tags.push(instance);
            }
          }

          return tags;
        };

        /**
         * @function save
         * @memberOf NotificationCtrl
         *
         * @description
         *   Creates a new notification.
         */
        $scope.save = function() {
          $scope.saving = 1;

          $scope.notification.instances = $scope.notification.instances
            .map(function(a) { return a.id; });

          if ($scope.notification.start && angular.isObject($scope.notification.start)) {
            $scope.notification.start = $scope.notification.start.toString();
          }

          if ($scope.notification.end && angular.isObject($scope.notification.end)) {
            $scope.notification.end = $scope.notification.end.toString();
          }

          http.post('manager_ws_notification_save', $scope.notification)
            .then(function(response) {
              messenger.post(response.data);

              if (response.status === 201) {
                var url = response.headers().location.replace('/manager', '');
                $location.path(url);
              }
            }, function(response) {
              messenger.post(response.data);
              $scope.saving = 0;
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
          $scope.saving = 1;

          $scope.notification.instances = $scope.notification.instances
            .map(function(a) { return a.id; });

          if ($scope.notification.start && angular.isObject($scope.notification.start)) {
            $scope.notification.start = $scope.notification.start.toString();
          }

          if ($scope.notification.end && angular.isObject($scope.notification.end)) {
            $scope.notification.end = $scope.notification.end.toString();
          }

          var route = {
            name: 'manager_ws_notification_update',
            params: { id:  $scope.notification.id }
          };

          http.put(route, $scope.notification).then(function(response) {
            messenger.post(response.data);
            $scope.saving = 0;
          }, function(response) {
            messenger.post(response.data);
            $scope.saving = 0;
          });
        };

        $scope.$on('$destroy', function() {
          $scope.notification = null;
        });

        if ($routeParams.id) {
          itemService.show('manager_ws_notification_show', $routeParams.id).then(
            function(response) {
              $scope.extra        = response.data.extra;
              $scope.notification = response.data.notification;
            }
          );
        } else {
          itemService.new('manager_ws_notification_new').then(
            function(response) {
              $scope.extra = response.data.extra;
            }
          );
        }
      }
    ]);
})();
