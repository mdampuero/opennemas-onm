(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  UserCtrl
     *
     * @requires $location
     * @requires $routeParams
     * @requires $scope
     * @requires http
     * @requires routing
     * @requires messenger
     *
     * @description
     *   Handles all actions in users listing.
     */
    .controller('UserCtrl', [
      '$location', '$routeParams', '$scope', 'http', 'routing', 'messenger',
      function ($location, $routeParams, $scope, http, routing, messenger) {
        /**
         * @memberOf UserCtrl
         *
         * @description
         *  The user object.
         *
         * @type {Object}
         */
        $scope.user = {
          activated:     true,
          password:      null,
          type:          0,
          fk_user_group: [],
          user_language: 'default',
        };

        /**
         * @function getExtensions
         * @memberOf UserCtrl
         *
         * @description
         *   Search user groups that macht the query.
         *
         * @param {String} query The query to match.
         */
        $scope.getExtensions = function(query) {
          var tags = [];

          var route = {
            name:   'manager_ws_modules_list',
            params: { oql: 'uuid ~ "' + query + '" limit 10' }
          };

          return http.get(route).then(function(response) {
            for (var i = 0; i < response.data.results.length;  i++) {
              tags.push(response.data.results[i].uuid);
            }

            return tags;
          });
        };

        /**
         * @function getGroups
         * @memberOf UserCtrl
         *
         * @description
         *   Search user groups that macht the query.
         *
         * @param {String} query The query to match.
         */
        $scope.getGroups = function(query) {
          var tags = [];

          for (var i = 0; i < $scope.extra.user_groups.length;  i++) {
            var name = $scope.extra.user_groups[i].name.toLowerCase();
            if (name.indexOf(query.toLowerCase()) !== -1) {
              tags.push($scope.extra.user_groups[i]);
            }
          }

          return tags;
        };

        /**
         * @function save
         * @memberOf UserCtrl
         *
         * @description
         *   Saves a new user.
         */
        $scope.save = function() {
          $scope.saving = 1;

          var data  = angular.copy($scope.user);

          if (data.fk_user_group) {
            data.fk_user_group = data.fk_user_group
              .map(function(e) { return e.pk_user_group; });
          }

          if (data.extensions) {
            data.extensions = data.extensions
              .map(function(e) { return e.name; });
          }

          http.post('manager_ws_user_save', data).then(function (response) {
            messenger.post(response.data);

            if (response.status === 201) {
              var url = response.headers().location.replace('/managerws', '');
              $location.path(url);
            }
          }, function(response) {
            messenger.post(response.data);
            $scope.saving = 0;
          });
        };

        /**
         * @function update
         * @memberOf UserCtrl
         *
         * @description
         *   Updates an user.
         */
        $scope.update = function() {
          $scope.saving = 1;

          var data  = angular.copy($scope.user);
          var route = {
            name: 'manager_ws_user_update',
            params: { id: $scope.user.id }
          };

          if (data.fk_user_group) {
            data.fk_user_group = data.fk_user_group
              .map(function(e) { return e.pk_user_group; });
          }

          if (data.extensions) {
            data.extensions = data.extensions
              .map(function(e) { return e.name; });
          }

          http.put(route, data).then(function (response) {
            messenger.post(response.data);
            $scope.saving = 0;
          }, function(response) {
            messenger.post(response.data);
            $scope.saving = 0;
          });
        };

        // Frees up memory on destroy event
        $scope.$on('$destroy', function() {
          $scope.user  = null;
          $scope.extra = null;
        });

        var route = 'manager_ws_user_new';

        if ($routeParams.id) {
          route = {
            name:   'manager_ws_user_show',
            params: { id:  $routeParams.id }
          };
        }

        http.get(route).then(function(response) {
          $scope.extra  = response.data.extra;

          if (response.data.user) {
            $scope.user = angular.merge($scope.user, response.data.user);

            $scope.user.fk_user_group = $scope.user.fk_user_group
              .map(function (e) {
                for (var i = 0; i < $scope.extra.user_groups.length; i++) {
                  if ($scope.extra.user_groups[i].pk_user_group === parseInt(e)) {
                    return $scope.extra.user_groups[i];
                  }
                }
              });
          }
        });
      }
  ]);
})();
