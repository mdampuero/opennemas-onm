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
          create:   'api_v1_backend_user_create',
          redirect: 'backend_user_show',
          save:     'api_v1_backend_user_save',
          show:     'api_v1_backend_user_show',
          update:   'api_v1_backend_user_update'
        };

        /**
         * @function areAllCategoriesSelected
         * @memberOf AdvertisementCtrl
         *
         * @description
         *   Checks if all user groups are selected.
         *
         * @return {Boolean} True if all user groups are selected. False
         *                   otherwise.
         */
        $scope.areAllCategoriesSelected = function() {
          if ($scope.flags.categories.all) {
            $scope.item.categories = $scope.data.extra.categories
              .map(function(e) {
                return e.id;
              });

            return;
          }

          $scope.item.categories = [];
        };

        /**
         * @function confirmUser
         * @memberOf UserCtrl
         *
         * @description
         *   Shows a modal to confirm user update.
         */
        $scope.confirm = function() {
          if ($scope.master || !$scope.item.activated ||
              $scope.item.activated === $scope.backup.activated) {
            $scope.save();
            $scope.backup.activated = $scope.item.activated;
            return;
          }

          var modal = $uibModal.open({
            templateUrl: 'modal-confirm',
            backdrop: 'static',
            controller: 'modalCtrl',
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
                    name: 'api_v1_backend_user_update',
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
         * @function getUsername
         * @memberOf UserCtrl
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
         * @function parseItem
         * @memberOf UserCtrl
         *
         * @description
         *   Parses the response and adds information to the scope.
         *
         * @param {Object} data The data in the response.
         */
        $scope.parseItem = function(data) {
          if (data.item) {
            $scope.item   = angular.extend($scope.item, data.item);
            $scope.backup = { activated: $scope.item.activated };
          }

          $scope.flags.categories = { none: false, all: false };

          if (!$scope.item.user_groups) {
            $scope.item.user_groups = {};
          }

          for (var id in $scope.data.extra.user_groups) {
            if (!$scope.item.user_groups[id]) {
              $scope.item.user_groups[id] = {
                expires: null,
                status: 0,
                user_group_id: id
              };
            }
          }

          if (!$scope.item.categories ||
              $scope.item.categories.length === 0) {
            $scope.flags.categories = { none: true };
          }

          if (data.extra.photos &&
              data.extra.photos[$scope.item.avatar_img_id]) {
            $scope.item.avatar_img_id =
              data.extra.photos[$scope.item.avatar_img_id];
          }
        };

        // Removes categories from item when flag changes
        $scope.$watch('flags.categories.none', function(nv) {
          if (nv) {
            $scope.item.categories = [];
          }
        });

        // Updates flag for "Select/deselect all" control when categories change
        $scope.$watch('item.categories', function(nv) {
          if (!$scope.flags.categories) {
            return;
          }

          $scope.flags.categories.all = false;

          if (nv.length === Object.keys($scope.data.extra.categories).length) {
            $scope.flags.categories.all = true;
          }
        }, true);
      }
    ]);
})();
