(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  UserCtrl
     *
     * @requires $filter
     * @requires $location
     * @requires $routeParams
     * @requires $scope
     * @requires itemService
     * @requires routing
     * @requires messenger
     *
     * @description
     *   Handles all actions in users listing.
     */
    .controller('UserCtrl', [
      '$filter', '$location', '$routeParams', '$scope', 'itemService', 'routing', 'messenger',
      function ($filter, $location, $routeParams, $scope, itemService, routing, messenger) {
        /**
         * Creates a new user.
         */
        $scope.save = function() {
          if ($scope.userForm.$invalid) {
            $scope.formValidated = 1;

            messenger.post({
              message: $filter('translate')('FormErrors'),
              type:    'error'
            });

            return false;
          }

          $scope.saving = 1;

          if ($scope.user.meta.paywall_time_limit) {
            $scope.user.meta.paywall_time_limit = $scope.user.meta.paywall_time_limit.toString();
          }

          itemService.save('manager_ws_user_create', $scope.user)
            .then(function (response) {
              messenger.post({
                message: response.data,
                type: response.status === 201  ? 'success' : 'error'
              });

              if (response.status === 201) {
                // Get new user id
                var url = response.headers()['location'];
                var id  = url.substr(url.lastIndexOf('/') + 1);

                url = routing.ngGenerateShort(
                    'manager_user_show', { id: id });
                $location.path(url);
              }

              $scope.saving = 0;
            });
        };

        /**
         * Updates an user.
         */
        $scope.update = function() {
          if ($scope.userForm.$invalid) {
            $scope.formValidated = 1;

            messenger.post({
              message: $filter('translate')('FormErrors'),
              type:    'error'
            });

            return false;
          }

          $scope.saving = 1;

          if ($scope.user.meta.paywall_time_limit) {
            $scope.user.meta.paywall_time_limit = $scope.user.meta.paywall_time_limit.toString();
          }

          itemService.update('manager_ws_user_update', $scope.user.id,
              $scope.user).then(function (response) {
            messenger.post({
              message: response.data,
              type: response.status === 200 ? 'success' : 'error'
            });

            $scope.saving = 0;
          });
        };

        $scope.$on('$destroy', function() {
          $scope.user  = null;
          $scope.extra = null;
        });

        //  Initialize user
        if ($routeParams.id) {
          itemService.show('manager_ws_user_show', $routeParams.id).then(
            function(response) {
              console.log(response)
              $scope.user = response.data.user;
              $scope.extra = response.data.extra;

              if (!angular.isArray($scope.user.meta)) {
                $scope.user.meta = { user_language: 'default' };
              }
            }
          );
        } else {
          itemService.new('manager_ws_user_new').then(function(response) {
            $scope.extra = response.data.extra;
            $scope.user = { meta: { user_language: 'default' } };
          });
        }
      }
  ]);
})();
