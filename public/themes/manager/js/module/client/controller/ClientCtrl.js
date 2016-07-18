(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  ClientCtrl
     *
     * @requires $filter
     * @requires $location
     * @requires $scope
     * @requires http
     * @requires routing
     * @requires messenger
     * @requires data
     *
     * @description
     *   Handles actions for instance edition form
     */
    .controller('ClientCtrl', [
      '$location', '$routeParams', '$scope', 'http', 'routing', 'messenger',
      function ($location, $routeParams, $scope, http, routing, messenger) {
        /**
         * @memberOf ClientCtrl
         *
         * @description
         *  The client object.
         *
         * @type {Object}
         */
        $scope.client = {};

        /**
         * @function save
         * @memberOf ClientCtrl
         *
         * @description
         *   Saves the client.
         */
        $scope.save = function() {
          $scope.saving = 1;

          http.post('manager_ws_client_save', $scope.client)
            .then(function (response) {
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
         * @memberOf ClientCtrl
         *
         * @description
         *   Updates a client.
         */
        $scope.update = function() {
          $scope.saving = 1;

          var route = {
            name: 'manager_ws_client_update',
            params: { id: $scope.client.id }
          };

          http.put(route, $scope.client, $scope.client)
            .then(function (response) {
              messenger.post(response.data);
              $scope.saving = 0;
            }, function(response) {
              messenger.post(response.data);
              $scope.saving = 0;
            });
        };

        var route = 'manager_ws_client_new';

        if ($routeParams.id) {
          route = {
            name:   'manager_ws_client_show',
            params: { id:  $routeParams.id }
          };
        }

        http.get(route).then(function(response) {
          $scope.extra  = response.data.extra;

          if (response.data.client) {
            $scope.client = angular.merge($scope.client, response.data.client);
          }
        });
      }
    ]);
})();

