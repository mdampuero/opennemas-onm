(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SubscriptionCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $window
     * @requires cleaner
     * @requires http
     * @requires messenger
     * @requires routing
     *
     * @description
     *   Provides actions to edit, save and update subscriptions.
     */
    .controller('SubscriptionCtrl', [
      '$controller', '$scope', '$window', 'cleaner', 'http', 'messenger', 'routing',
      function($controller, $scope, $window, cleaner, http, messenger, routing) {
        // Initialize the super class and extend it
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf SubscriptionCtrl
         *
         * @description
         *  The subscription object.
         *
         * @type {Object}
         */
        $scope.item = {
          privileges: [],
          type: 1
        };

        /**
         * @function getItem
         * @memberOf SubscriptionCtrl
         *
         * @description
         *   Gets the subscription to show.
         *
         * @param {Integer} id The subscription id.
         */
        $scope.getItem = function(id) {
          $scope.flags.loading = 1;

          var route = !id ? 'api_v1_backend_subscription_create' :
            { name: 'api_v1_backend_subscription_show', params: { id: id } };

          http.get(route).then(function(response) {
            $scope.data = response.data;

            if ($scope.data.subscription) {
              $scope.item = $scope.data.subscription;
            }

            $scope.disableFlags();
          }, function() {
            $scope.item = null;

            $scope.disableFlags();
          });
        };

        /**
         * @function init
         * @memberOf SubscriptionCtrl
         *
         * @description
         *   Initializes the subscription.
         *
         * @param {Integer} id The subscription id when editing.
         */
        $scope.init = function(id) {
          $scope.getItem(id);
        };

        /**
         * @function save
         * @memberOf SubscriptionCtrl
         *
         * @description
         *   Saves a new subscription.
         */
        $scope.save = function() {
          if ($scope.subscriptionForm.$invalid) {
            return;
          }

          $scope.subscriptionForm.$setPristine(true);
          $scope.flags.saving = true;

          var data = cleaner.clean($scope.item);

          /**
           * Callback executed when subscription is saved/updated successfully.
           *
           * @param {Object} response The response object.
           */
          var successCb = function(response) {
            $scope.disableFlags();

            if (response.status === 201) {
              var id = response.headers().location
                .substring(response.headers().location.lastIndexOf('/') + 1);

              $window.location.href =
                routing.generate('backend_subscription_show', { id: id });
            }

            messenger.post(response.data);
          };

          if (!$scope.item.pk_user_group) {
            var route = { name: 'api_v1_backend_subscription_create' };

            http.post(route, data).then(successCb, $scope.errorCb);
            return;
          }

          http.put({
            name: 'api_v1_backend_subscription_update',
            params: { id: $scope.item.pk_user_group }
          }, data).then(successCb, $scope.errorCb);
        };
      }
    ]);
})();
