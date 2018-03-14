(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SubscriberCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $uibModal
     * @requires $window
     * @requires cleaner
     * @requires http
     * @requires messenger
     *
     * @description
     *   Provides actions to edit, save and update subscribers.
     */
    .controller('SubscriberCtrl', [
      '$controller', '$scope', '$uibModal', '$window', 'cleaner', 'http', 'messenger', 'routing',
      function($controller, $scope, $uibModal, $window, cleaner, http, messenger, routing) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf SubscriberCtrl
         *
         * @description
         *  The subscriber object.
         *
         * @type {Object}
         */
        $scope.item = {
          name: '',
          type: 1,
          privileges: []
        };

        /**
         * @function convertTo
         * @memberOf SubscriberListCtrl
         *
         * @description
         *   Confirm delete action.
         */
        $scope.convertTo = function(property, value) {
          var modal = $uibModal.open({
            templateUrl: 'modal-convert',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { type: value };
              },
              success: function() {
                return function() {
                  var data  = angular.copy($scope.item);
                  var route = {
                    name: 'api_v1_backend_subscriber_update',
                    params: { id: $scope.item.id }
                  };

                  data.type = value;

                  if (value === 1) {
                    // Remove all subscriptions
                    data.fk_user_group = _.difference(
                      data.fk_user_group,
                      Object.keys($scope.data.extra.subscriptions));
                  }

                  data = cleaner.clean(data);

                  return http.put(route, data);
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              if (value === 0) {
                $window.location.href = routing.generate('admin_acl_user_show',
                  { id: $scope.item.id });
              }
            }
          });
        };

        /**
         * @function getItem
         * @memberOf SubscriberCtrl
         *
         * @description
         *   Gets the subscriber to show.
         *
         * @param {Integer} id The subscriber id.
         */
        $scope.getItem = function(id) {
          $scope.flags.loading = 1;

          var route = !id ? 'api_v1_backend_subscriber_new' :
            { name: 'api_v1_backend_subscriber_show', params: { id: id } };

          http.get(route).then(function(response) {
            $scope.data = response.data;

            if ($scope.data.subscriber) {
              $scope.item = $scope.data.subscriber;
            }

            $scope.disableFlags();
          }, function() {
            $scope.item = null;

            $scope.disableFlags();
          });
        };

        /**
         * @function init
         * @memberOf SubscriberListCtrl
         *
         * @description
         *   Initializes services and list subscribers.
         *
         * @param {Integer} id The subscriber id when editing.
         */
        $scope.init = function(id) {
          $scope.getItem(id);
        };

        /**
         * @function save
         * @memberOf SubscriberCtrl
         *
         * @description
         *   Saves a new subscriber.
         */
        $scope.save = function() {
          if ($scope.subscriberForm.$invalid) {
            return;
          }

          $scope.subscriberForm.$setPristine(true);
          $scope.flags.saving = true;

          var data = cleaner.clean($scope.item);

          /**
           * Callback executed when subscriber is saved/updated successfully.
           *
           * @param {Object} response The response object.
           */
          var successCb = function(response) {
            $scope.disableFlags();

            if (response.status === 201) {
              var id = response.headers().location
                .substring(response.headers().location.lastIndexOf('/') + 1);

              $window.location.href =
                routing.generate('backend_subscriber_show', { id: id });
            }

            messenger.post(response.data);
          };

          if (!$scope.item.id) {
            var route = { name: 'api_v1_backend_subscriber_create' };

            http.post(route, data).then(successCb, $scope.errorCb);
            return;
          }

          http.put({
            name: 'api_v1_backend_subscriber_update',
            params: { id: $scope.item.id }
          }, data).then(successCb, $scope.errorCb);
        };
      }
    ]);
})();
