(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  WebPushNotificationsListCtrl
     *
     * @requires $controller
     * @requires $location
     * @requires $scope
     * @requires $timeout
     * @requires http
     * @requires messenger
     * @requires linker
     * @requires localizer
     * @requires oqlEncoder
     *
     * @description
     *   Provides actions to list Web Push notifications.
     */
    .controller('WebPushNotificationsListCtrl', [
      '$controller', '$scope', '$uibModal', 'http', 'messenger', 'oqlEncoder', 'routing',
      function($controller, $scope, $uibModal, http, messenger, oqlEncoder, routing) {
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
          getList: 'api_v1_backend_webpush_notifications_get_list',
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
            .concat([ 'send_date', 'transaction_id' ]));

          oqlEncoder.configure({
            placeholder: {
              title: 'title ~ "%[value]%"',
              send_date: '[key] ~ "[value]"'
            }
          });

          $scope.list();
        };

        /**
         * @function parseList
         * @memberOf RestListCtrl
         *
         * @description
         *   Parses the response and adds information to the scope.
         *
         * @param {Object} data The data in the response.
         */
        $scope.parseList = function(data) {
          data.items.forEach(function(item) {
            item.image = Number(item.image);
          });
        };

        /**
         * @inheritdoc
         */
        $scope.isSelectable = function() {
          return false;
        };
      }
    ]);
})();
