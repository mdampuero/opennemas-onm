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
     *
     * @description
     *   Provides actions to edit, save and update subscriptions.
     */
    .controller('SubscriptionCtrl', [
      '$controller', '$scope', '$window', 'cleaner', 'http', 'messenger',
      function($controller, $scope, $window, cleaner, http, messenger) {
        // Initialize the super class and extend it.
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
          name: '',
          type: 1,
          privileges: []
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
            $scope.item = $scope.data.subscription;

            $scope.disableFlags();
          }, $scope.errorCb);
        };

        /**
         * @function init
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *   Initializes services and list subscriptions.
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
              $window.location.href = response.headers().location;
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
