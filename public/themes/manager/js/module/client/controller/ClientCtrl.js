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
     * @requires itemService
     * @requires routing
     * @requires messenger
     * @requires data
     *
     * @description
     *   Handles actions for instance edition form
     */
    .controller('ClientCtrl', [
      '$location', '$scope', 'itemService', 'routing', 'messenger', 'data',
      function ($location, $scope, itemService, routing, messenger, data) {
        /**
         * @memberOf ClientCtrl
         *
         * @description
         *  The client object.
         *
         * @type {Object}
         */
        $scope.client = data.client;

        /**
         * @memberOf ClientCtrl
         *
         * @description
         *  Extra data.
         *
         * @type {Object}
         */
        $scope.extra = data.extra;

        /**
         * @function save
         * @memberOf ClientCtrl
         *
         * @description
         *   Saves the client.
         */
        $scope.save = function() {
          $scope.saving = 1;

          itemService.save('manager_ws_client_save', $scope.client)
            .then(function (response) {
              messenger.post({ message: response.data, type: 'success' });

              if (response.status === 201) {
                // Get new client id
                var url = response.headers().location;
                var id  = url.substr(url.lastIndexOf('/') + 1);

                url = routing.ngGenerateShort('manager_client_show', { id: id });
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
         * @memberOf ClientCtrl
         *
         * @description
         *   Updates a client.
         */
        $scope.update = function() {
          $scope.saving = 1;

          itemService.update('manager_ws_client_update', $scope.client.id,
            $scope.client).success(function (response) {
              messenger.post({ message: response, type: 'success' });
              $scope.saving = 0;
            }).error(function(response) {
              messenger.post({ message: response, type: 'error' });
              $scope.saving = 0;
            });
        };
      }
    ]);
})();

