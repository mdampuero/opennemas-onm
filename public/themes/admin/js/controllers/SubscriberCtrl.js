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
         * @function accept
         * @memberOf SubscriberCtrl
         *
         * @description
         *   Accepts a subscription.
         *
         * @param {Integer} id The subscription id.
         */
        $scope.accept = function(id) {
          if (!$scope.item.user_groups[id]) {
            $scope.item.user_groups[id] = { status: 0, expires: null };
          }

          $scope.item.user_groups[id].status = 1;
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
                return {
                  item: $scope.item,
                  type: value
                };
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
                $window.location.href = routing.generate('backend_user_show',
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

            if (!$scope.item.user_groups) {
              $scope.item.user_groups = {};
            }

            for (var id in $scope.data.extra.subscriptions) {
              if (!$scope.item.user_groups[id]) {
                $scope.item.user_groups[id] = {
                  expires: null,
                  status: 0,
                  user_group_id: id
                };
              }
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
         * @function reject
         * @memberOf SubscriberCtrl
         *
         * @description
         *   Rejects a subscription.
         *
         * @param {Integer} id The subscription id.
         */
        $scope.reject = function(id) {
          delete $scope.item.user_groups[id];
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

          var data = cleaner.clean(angular.copy($scope.item));

          for (var key in data.user_groups) {
            if (data.user_groups[key].status === 0) {
              delete data.user_groups[key];
            }
          }

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
