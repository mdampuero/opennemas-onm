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
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf SubscriberCtrl
         *
         * @description
         *  The subscriber object.
         *
         * @type {Object}
         */
        $scope.item = {
          name: null,
          privileges: [],
          type: 1,
          user_groups: []
        };

        /**
         * @memberOf SubscriberCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          createItem: 'api_v1_backend_subscriber_create_item',
          getItem:    'api_v1_backend_subscriber_get_item',
          list:       'backend_subscribers_list',
          redirect:   'backend_subscriber_show',
          saveItem:   'api_v1_backend_subscriber_save_item',
          updateItem: 'api_v1_backend_subscriber_update_item'
        };

        /**
         * @function accept
         * @memberOf SubscriberCtrl
         *
         * @description
         *   Accepts a subscription.
         *
         * @param {Integer} index The position of the subscription in the list
         *                        of subscriptions.
         */
        $scope.accept = function(index) {
          if (!$scope.item.user_groups[index]) {
            $scope.item.user_groups[index] = { status: 0, expires: null };
          }

          $scope.item.user_groups[index].status = 1;
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          if (!$scope.item.user_groups) {
            $scope.item.user_groups = {};
          }

          var subscriptions        = Object.keys($scope.data.extra.subscriptions);
          var subscriptionsPresent = $scope.item.user_groups
            .map(function(subscription) {
              return subscription.user_group_id;
            });

          for (var index in subscriptions) {
            var id = parseInt(subscriptions[index]);

            if (subscriptionsPresent.indexOf(id) === -1) {
              $scope.item.user_groups.push({
                user_id: $scope.item.id,
                user_group_id: id,
                status: 0,
                expire: null
              });
            }
          }

          if ($scope.data.extra.photos &&
              $scope.data.extra.photos[$scope.item.avatar_img_id]) {
            $scope.item.avatar_img_id =
              $scope.data.extra.photos[$scope.item.avatar_img_id];
          }
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
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {
                  item: $scope.item,
                  type: value
                };
              },
              success: function() {
                return function() {
                  var data  = $scope.getData();
                  var route = {
                    name: $scope.routes.updateItem,
                    params: { id: $scope.item.id }
                  };

                  data.type = value;

                  if (value === 1) {
                    var ids = Object.keys($scope.data.extra.subscriptions);

                    // Remove all subscriptions
                    data.user_groups = data.user_groups.filter(function(group) {
                      return ids.indexOf(group.user_group_id) !== -1;
                    });
                  }

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
         * @function getData
         * @memberOf SubscriberCtrl
         *
         * @description
         *   Returns the data to send when saving/updating a subscriber.
         */
        $scope.getData = function() {
          var data = cleaner.clean(angular.copy($scope.item));

          // The call to angular.copy does not copy files
          if (data.avatar_img_id instanceof Object) {
            data.avatar_img_id = data.avatar_img_id.pk_content;
          }

          for (var key in data.user_groups) {
            if (!data.user_groups[key] || data.user_groups[key].status === 0) {
              delete data.user_groups[key];
            }
          }

          return data;
        };

        /**
         * @function reject
         * @memberOf SubscriberCtrl
         *
         * @description
         *   Rejects a subscription.
         *
         * @param {Integer} index The position of the subscription in the list
         *                        of subscriptions.
         */
        $scope.reject = function(index) {
          $scope.item.user_groups[index].status = 0;
        };
      }
    ]);
})();
