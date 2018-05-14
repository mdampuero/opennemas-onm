(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  AuthorCtrl
     *
     * @requires $controller
     * @requires $http
     * @requires $uibModal
     * @requires $scope
     *
     * @description
     *   Check billing information when saving author.
     */
    .controller('AuthorCtrl', [
      '$controller', '$http', '$scope', '$timeout', '$uibModal', 'cleaner',
      function($controller, $http, $scope, $timeout, $uibModal, cleaner) {
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf AuthorCtrl
         *
         * @description
         *  The author object.
         *
         * @type {Object}
         */
        $scope.item = {
          name: null,
          type: 0,
          user_groups: {
            3: {
              expires: null,
              status: 1,
              user_group_id: 3
            }
          }
        };

        /**
         * @memberOf AuthorCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          create:   'api_v1_backend_author_create',
          redirect: 'backend_author_show',
          save:     'api_v1_backend_author_save',
          show:     'api_v1_backend_author_show',
          update:   'api_v1_backend_author_update'
        };

        /**
         * @function getUsername
         * @memberOf AuthorCtrl
         *
         * @description
         *   Generates an username basing on the name.
         */
        $scope.getUsername = function() {
          if ($scope.item.username) {
            return;
          }

          $scope.flags.http.slug = 1;

          if ($scope.tm) {
            $timeout.cancel($scope.tm);
          }

          $scope.tm = $timeout(function() {
            $scope.getSlug($scope.item.name, function(response) {
              $scope.item.username = response.data.slug;
              $scope.form.username.$setDirty(true);
            });
          }, 500);
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
            if (!data.user_groups[key] || data.user_groups[key].status === 0) {
              delete data.user_groups[key];
              continue;
            }

            data.fk_user_group.push(key);
          }

          return data;
        };

        /**
         * @function parseItem
         * @memberOf AuthorCtrl
         *
         * @description
         *   Parses the response and adds information to the scope.
         *
         * @param {Object} data The data in the response.
         */
        $scope.parseItem = function(data) {
          if (data.item) {
            $scope.item = angular.extend($scope.item, data.item);
          }

          if (!$scope.item.user_groups) {
            $scope.item.user_groups = {};
          }

          $scope.item.user_groups[3] = {
            expires: null,
            status: 1,
            user_group_id: 3
          };

          if (data.extra.photos &&
            data.extra.photos[$scope.item.avatar_img_id]) {
            $scope.item.avatar_img_id =
              data.extra.photos[$scope.item.avatar_img_id];
          }
        };
      }
    ]);
})();
