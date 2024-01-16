(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  WebPushNotificationsListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     * @requires messenger
     * @requires oqlEncoder
     * @requires $location
     *
     * @description
     *   Provides actions to list Web Push notifications.
     */
    .controller('WebPushNotificationsListCtrl', [
      '$controller', '$scope', 'http', 'messenger', 'oqlEncoder', '$location',
      function($controller, $scope, http, messenger, oqlEncoder, $location) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          epp: 10,
          orderBy: { id:  'desc' },
          page: 1,
        };

        /**
         * @memberOf WebPushNotificationsListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          getList:             'api_v1_backend_webpush_notifications_get_list',
          getNotificationData: 'api_v1_backend_webpush_notifications_get_notification_data'
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.expandFields();

          if ($scope.data.extra.photos && $scope.data.extra.photos[$scope.item.image]) {
            $scope.item.avatar_img_id = $scope.data.extra.photos[$scope.item.image];
          }
        };

        /**
         * @function init
         * @memberOf WebPushNotificationsListCtrl
         *
         * @description
         *   Configures and initializes the list.
         */
        $scope.init = function() {
          $scope.backup.criteria    = $scope.criteria;
          $scope.app.columns.hidden = [ 'category', 'tags', 'content_views', 'created', 'changed', 'author', 'starttime', 'endtime' ];
          $scope.app.columns.selected =  _.uniq($scope.app.columns.selected
            .concat([ 'send_date', 'transaction_id', 'impressions', 'ctr', 'clicks' ]));

          oqlEncoder.configure({
            placeholder: {
              title: 'title ~ "%[value]%"',
              send_date: '[key] ~ "[value]"'
            }
          });

          $scope.list();
        };

        /**
         * @function list
         * @memberOf RestListCtrl
         *
         * @description
         *   Reloads the list.
         */
        $scope.list = function() {
          $scope.flags.http.loading = 1;

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: $scope.routes.getList,
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            $scope.data = response.data;

            if (!response.data.items) {
              $scope.data.items = [];
            }

            $scope.items = $scope.data.items;

            $scope.disableFlags('http');
          }, function(response) {
            messenger.post(response.data);
            $scope.disableFlags('http');
            $scope.items = [];
          });
        };

        /**
         * @inheritdoc
         */
        $scope.isSelectable = function() {
          return false;
        };

        /**
         * @function check
         * @memberOf WebPushNotificationsListCtrl
         *
         * @description
         *   Retrieve data from the given notification.
         */
        $scope.getNotificationData = function(item) {
          $scope.flags.http.checking = true;

          var route = {
            name: $scope.routes.getNotificationData,
            params: { id: item.transaction_id }
          };

          http.get(route).then(function(response) {
            item.notificationData = response.data;
            $scope.disableFlags('http');
            $scope.status = 'success';
          }, function() {
            $scope.disableFlags('http');
            $scope.status = 'failure';
          });
        };
      }
    ]);
})();
