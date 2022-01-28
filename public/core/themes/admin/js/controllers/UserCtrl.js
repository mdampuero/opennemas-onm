(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  UserCtrl
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
     *   Check billing information when saving user.
     */
    .controller('UserCtrl', [
      '$controller', '$scope', '$timeout', '$uibModal', '$window', 'cleaner', 'http', 'messenger', 'routing',
      function($controller, $scope, $timeout, $uibModal, $window, cleaner, http, messenger, routing) {
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf UserCtrl
         *
         * @description
         *  The user object.
         *
         * @type {Object}
         */
        $scope.item = {
          categories: [],
          name: null,
          type: 0,
          user_groups: [],
          user_language: 'default'
        };

        /**
         * @memberOf UserCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          createItem: 'api_v1_backend_user_create_item',
          getItem:    'api_v1_backend_user_get_item',
          list:       'backend_users_list',
          redirect:   'backend_user_show',
          saveItem:   'api_v1_backend_user_save_item',
          updateItem: 'api_v1_backend_user_update_item'
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.backup.activated = $scope.item.activated;
          $scope.flags.categories = { none: false, all: false };

          if (!$scope.item.user_groups) {
            $scope.item.user_groups = {};
          }

          var userGroups = Object.keys($scope.data.extra.user_groups);
          var userGroupsPresent =  $scope.item.user_groups.map(function(userGroup) {
            return userGroup.user_group_id;
          });

          for (var index in userGroups) {
            if (userGroupsPresent.indexOf(parseInt(userGroups[index])) === -1) {
              $scope.item.user_groups.push({
                user_id: $scope.item.id,
                user_group_id: parseInt(userGroups[index]),
                status: 0,
                expire: null
              });
            }
          }

          if (!$scope.item.categories ||
              $scope.item.categories.length === 0) {
            $scope.flags.categories = { none: true };
          }

          if ($scope.data.extra.photos &&
              $scope.data.extra.photos[$scope.item.avatar_img_id]) {
            $scope.item.avatar_img_id =
              $scope.data.extra.photos[$scope.item.avatar_img_id];
          }
        };

        /**
         * @function confirmUser
         * @memberOf UserCtrl
         *
         * @description
         *   Shows a modal to confirm user update.
         */
        $scope.confirm = function() {
          if ($scope.backup.master || !$scope.item.activated ||
              $scope.item.activated === $scope.backup.activated) {
            $scope.save();
            $scope.backup.activated = $scope.item.activated;
            return;
          }

          var modal = $uibModal.open({
            templateUrl: 'modal-confirm',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return {
                  name: $scope.id ? 'update' : 'create',
                  backend_access: true,
                  value: 1,
                  extra: $scope.data.extra,
                };
              },
              success: function() {
                return null;
              }
            }
          });

          modal.result.then(function(response) {
            if (response) {
              $scope.save();
              $scope.backup.activated = $scope.item.activated;
            }
          });
        };

        /**
         * @function convertTo
         * @memberOf UserCtrl
         *
         * @description
         *   Opens a modal to confirm user conversion.
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

                  // Remove all the user groups if the user is changed to only subscriber.
                  if (value === 1) {
                    data.user_groups = data.user_groups.filter(function(group) {
                      return !$scope.isUserGroup(group);
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
              if (value === 1) {
                $window.location.href = routing.generate(
                  'backend_subscriber_show', { id: $scope.item.id });
              }
            }
          });
        };

        /**
         * @funcion isUserGroup
         * @memberOf UserCtrl
         *
         * @param {Object} userGroup The user group to filter.
         *
         * @returns {Boolean} True if the user group is not a subscription.
         */
        $scope.isUserGroup = function(userGroup) {
          return Object.keys($scope.data.extra.user_groups).includes(String(userGroup.user_group_id));
        };

        /**
         * @function countUserGroups
         * @memberOf UserCtrl
         *
         * @description
         *   Counts the number of user groups for the item ignoring the
         *   subscriptions.
         *
         * @param {Object} item The item.
         *
         * @return {Integer} The number of user groups.
         */
        $scope.countUserGroups = function(item) {
          if (!$scope.data || !$scope.data.extra.user_groups) {
            return 0;
          }

          var keys = Object.keys($scope.data.extra.user_groups)
            .map(function(e) {
              return parseInt(e);
            });

          var ids  = $scope.toArray(item.user_groups).filter(function(e) {
            return e.status === 1 &&
              keys.indexOf(parseInt(e.user_group_id)) !== -1;
          });

          return ids.length;
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
         * @function getUsername
         * @memberOf UserCtrl
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

        // Removes categories from item when flag changes
        $scope.$watch('flags.categories.none', function(nv) {
          if (nv) {
            $scope.item.categories = [];
          }
        });
      }
    ]);
})();
