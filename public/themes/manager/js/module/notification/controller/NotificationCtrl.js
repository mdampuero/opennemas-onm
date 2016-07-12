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
      '$location', '$routeParams', '$scope', 'http', 'messenger',
      function ($location, $routeParams, $scope, http, messenger) {
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
          target: [],
          enabled: '0',
          fixed: '0',
          forced: '0',
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
         * @function autocomplete
         * @memberOf NotificationCtrl
         *
         * @description
         *   Suggest a list of instance basing on the current query.
         *
         * @return {Array} A list of targets
         */
        $scope.autocomplete = function(query) {
          var route = {
              name: 'manager_ws_notification_autocomplete',
              params: { query: query }
          }; 

          return http.get(route).then(function(response) {
              var tags = [];

              for (var i = 0; i < response.data.target.length; i++) {
                tags.push(response.data.target[i]);
              }

              return tags;
            });
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
         * @function save
         * @memberOf NotificationCtrl
         *
         * @description
         *   Creates a new notification.
         */
        $scope.save = function() {
          $scope.saving = 1;

          var data = angular.copy($scope.notification);

          if (data.target) {
            data.target = data.target.map(function(a) {
              return a.id;
            });
          }

          if (data.start && angular.isObject(data.start)) {
            data.start = data.start.toString();
          }

          if (data.end && angular.isObject(data.end)) {
            data.end = data.end.toString();
          }
          
          http.post('manager_ws_notification_save', $scope.notification)
            .then(function(response) {
              messenger.post(response.data);

              if (response.status === 201) {
                var url = response.headers().location.replace('/manager', '');
                $location.path(url);
              }

              $scope.saving = 0;
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

          var data = angular.copy($scope.notification);

          if (data.target) {
            data.target = data.target.map(function(a) {
              return a.id;
            });
          }

          if (data.start && angular.isObject(data.start)) {
            data.start = data.start.toString();
          }

          if (data.end && angular.isObject(data.end)) {
            data.end = data.end.toString();
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

              var target = [];

              for (var i = 0; i < $scope.notification.target.length; i++) {
                var id   = $scope.notification.target[i];
                var name = id;

                if (name === 'all') {
                  name = $scope.extra.target.filter(function (e) {
                    return e.id === id;
                  })[0].name;
                }

                target.push({ id: id, name: name });
              }

              $scope.notification.target = target;
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
