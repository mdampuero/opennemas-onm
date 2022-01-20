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
      '$controller', '$scope', '$timeout', 'cleaner',
      function($controller, $scope, $timeout, cleaner) {
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        $scope.expanded = {};

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
          createItem: 'api_v1_backend_author_create_item',
          getItem:    'api_v1_backend_author_get_item',
          list:       'backend_authors_list',
          redirect:   'backend_author_show',
          saveItem:   'api_v1_backend_author_save_item',
          updateItem: 'api_v1_backend_author_update_item'
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.expandFields();

          if (!$scope.item.user_groups) {
            $scope.item.user_groups = {};
          }

          $scope.item.user_groups[3] = {
            expires: null,
            status: 1,
            user_group_id: 3
          };

          if ($scope.data.extra.photos &&
              $scope.data.extra.photos[$scope.item.avatar_img_id]) {
            $scope.item.avatar_img_id =
              $scope.data.extra.photos[$scope.item.avatar_img_id];
          }
        };

        /**
         * @function getUsername
         * @memberOf AuthorCtrl
         *
         * @description
         *   Generates an username basing on the name.
         */
        $scope.getUsername = function() {
          if ($scope.item.username && $scope.item.slug) {
            return;
          }

          $scope.flags.http.slug = 1;

          if ($scope.tm) {
            $timeout.cancel($scope.tm);
          }

          $scope.tm = $timeout(function() {
            $scope.getSlug($scope.item.name, function(response) {
              if (!$scope.item.username) {
                $scope.item.username = response.data.slug;
                $scope.form.username.$setDirty(true);
              }

              if (!$scope.item.slug) {
                $scope.item.slug = response.data.slug;
                $scope.form.slug.$setDirty(true);
              }
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
            data.avatar_img_id = data.avatar_img_id.pk_content;
          }

          for (var key in data.user_groups) {
            if (!data.user_groups[key] || data.user_groups[key].status === 0) {
              delete data.user_groups[key];
            }
          }

          return data;
        };
      }
    ]);
})();
