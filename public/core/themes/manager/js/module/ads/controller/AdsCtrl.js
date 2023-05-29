(function() {
  'use strict';

  angular.module('ManagerApp.controllers')

    /**
     * @ngdoc controller
     * @name  AdsCtrl
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
    .controller('AdsCtrl', [
      '$location', '$routeParams', '$scope', 'http', 'routing', 'messenger',
      function($location, $routeParams, $scope, http, routing, messenger) {
        /**
         * @memberOf AdsCtrl
         *
         * @description
         *  The client object.
         *
         * @type {Object}
         */
        $scope.item = {
          instances: []
        };

        /**
         * @function save
         * @memberOf AdsCtrl
         *
         * @description
         *   Saves the client.
         */
        $scope.save = function() {
          $scope.saving = 1;
          var data = angular.copy($scope.item);

          if (data.instances) {
            data.instances = data.instances.map(function(a) {
              return a.name;
            });
          }
          http.post('manager_ws_ads_save', data)
            .then(function(response) {
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
            name: 'manager_ws_ads_autocomplete',
            params: { query: query }
          };

          return http.get(route).then(function(response) {
            return response.data.target;
          });
        };

        /**
         * @function update
         * @memberOf container
         *
         * @description
         *   Updates a item.
         */
        $scope.update = function() {
          $scope.saving = 1;

          var data = angular.copy($scope.item);

          if (data.instances) {
            data.instances = data.instances.map(function(a) {
              return a.name;
            });
          }
          var route = {
            name: 'manager_ws_ads_update',
            params: { id: $scope.item.id }
          };

          http.put(route, data)
            .then(function(response) {
              messenger.post(response.data);
              $scope.saving = 0;
            }, function(response) {
              messenger.post(response.data);
              $scope.saving = 0;
            });
        };

        $scope.$on('$destroy', function() {
          $scope.item = null;
        });

        var route = 'manager_ws_ads_new';

        if ($routeParams.id) {
          route = {
            name:   'manager_ws_ads_show',
            params: { id:  $routeParams.id }
          };
        }

        http.get(route).then(function(response) {
          $scope.extra  = response.data.extra;
          if (response.data.item) {
            $scope.item = response.data.item;
          }
        });
      }
    ]);
})();

