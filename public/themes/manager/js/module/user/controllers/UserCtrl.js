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
          enabled:        true,
          password:       null,
          type:           0,
          user_group_ids: [],
          user_language: 'default',
        };

        /**
         * @function autocomplete
         * @memberOf UserCtrl
         *
         * @description
         *   Adds a new price to the list.
         */
        $scope.autocomplete = function(query) {
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

          if (data.user_group_ids) {
            data.user_group_ids = data.user_group_ids
              .map(function(e) { return e.id; });
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

          if (data.user_group_ids) {
            data.user_group_ids = data.user_group_ids
              .map(function(e) { return e.id; });
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

            $scope.user.user_group_ids = $scope.user.user_group_ids
              .map(function (e) {
                for (var i = 0; i < $scope.extra.user_groups.length; i++) {
                  if ($scope.extra.user_groups[i].id === parseInt(e)) {
                    return $scope.extra.user_groups[i];
                  }
                }
              });
          }
        });
      }
  ]);
})();
