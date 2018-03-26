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
          name: '',
          privileges: [],
          type: 1
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
          create:   'api_v1_backend_subscriber_create',
          redirect: 'backend_subscriber_show',
          save:     'api_v1_backend_subscriber_save',
          show:     'api_v1_backend_subscriber_show',
          update:   'api_v1_backend_subscriber_update'
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
                  var data  = $scope.getData();
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
            data.avatar_img_id = data.avatar_img_id.pk_photo;
          }

          data.fk_user_group = [];
          for (var key in data.user_groups) {
            if (data.user_groups[key].status === 0) {
              delete data.user_groups[key];
              continue;
            }

            data.fk_user_group.push(key);
          }

          return data;
        };

        /**
         * @function parseItem
         * @memberOf SubscriberCtrl
         *
         * @description
         *   Gets the subscriber to show.
         *
         * @param {Integer} id The subscriber id.
         */
        $scope.parseItem = function(data) {
          if (data.item) {
            $scope.item = angular.extend($scope.item, data.item);
          }

          if (!$scope.item.user_groups) {
            $scope.item.user_groups = {};
          }

          for (var id in data.extra.subscriptions) {
            if (!$scope.item.user_groups[id]) {
              $scope.item.user_groups[id] = {
                expires: null,
                status: 0,
                user_group_id: id
              };
            }
          }

          if (data.extra.photos &&
              data.extra.photos[$scope.item.avatar_img_id]) {
            $scope.item.avatar_img_id =
              data.extra.photos[$scope.item.avatar_img_id];
          }
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
      }
    ]);
})();
