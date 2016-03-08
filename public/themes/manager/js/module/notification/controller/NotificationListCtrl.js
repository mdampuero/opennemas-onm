(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  NotificationListCtrl
     *
     * @requires $controller
     * @requires $uibModal
     * @requires $scope
     * @requires itemService
     * @requires routing
     * @requires messenger
     * @requires webStorage
     * @requires data
     *
     * @description
     *   Handles all actions in notifications listing.
     */
    .controller('NotificationListCtrl', [
      '$controller', '$uibModal', '$scope', '$timeout', 'itemService', 'routing', 'messenger', 'webStorage', 'data',
      function($controller, $uibModal, $scope, $timeout, itemService, routing, messenger, webStorage, data) {

        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope:   $scope,
          $timeout: $timeout,
          data:     data
        }));

        /**
         * @memberOf NotificationListCtrl
         *
         * @description
         *   The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = { name_like: [] };

        /**
         * @memberOf NotificationListCtrl
         *
         * @description
         *   The visible table columns.
         *
         * @type {Object}
         */
        $scope.columns = {
          collapsed: 1,
          selected: [
            'name', 'domains', 'last_login', 'created',
            'articles', 'alexa', 'activated'
          ]
        };

        /**
         * @memberOf NotificationListCtrl
         *
         * @description
         *   The listing order.
         *
         * @type {Object}
         */
        $scope.orderBy = [{ name: 'start', value: 'desc' }];

        /**
         * @function countStringsLeft
         * @memberOf ModuleListCtrl
         *
         * @description
         *   Counts the number of remaining strings for a language.
         *
         * @param {Object} item The item to check.
         *
         * @return {Integer} The number of remaining strings.
         */
        $scope.countStringsLeft = function(item) {
          var left = 0;

          for (var lang in $scope.extra.languages) {
            if (!item.title || !item.title[lang]) {
              left++;
            }

            if (!item.body || !item.body[lang]) {
              left++;
            }
          }

          return left;
        };

        /**
         * @function delete
         * @memberOf NotificationListCtrl
         *
         * @description
         *   Confirm delete action.
         *
         * @param {Object notification The notification to delete.
         */
        $scope.delete = function(notification) {
          var modal = $uibModal.open({
            templateUrl: '/managerws/template/notification:modal.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { };
              },
              success: function() {
                return function(modalNotification) {
                  itemService.delete('manager_ws_notification_delete', notification.id)
                    .success(function(response) {
                      modalNotification.close({ message: response, type: 'success'});
                    }).error(function(response) {
                      modalNotification.close({ message: response, type: 'error'});
                    });
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response);
            $scope.list();
          });
        };

        /**
         * @function deleteSelected
         * @memberOf NotificationListCtrl
         *
         * @description
         *   Confirm delete action.
         */
        $scope.deleteSelected = function() {
          var modal = $uibModal.open({
            templateUrl: '/managerws/template/notification:modal.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { selected: $scope.selected.items.length };
              },
              success: function() {
                return function(modalNotification) {
                  itemService.deleteSelected('manager_ws_notifications_delete',
                    $scope.selected.items).success(function(response) {
                      modalNotification.close(response);
                    }).error(function(response) {
                      modalNotification.close(response);
                    });
                };
              }
            }
          });

          modal.result.then(function(response) {
            if (response.messages) {
              messenger.post(response.messages);

              $scope.selected = { all: false, items: [] };
            } else {
              messenger.post(response);
            }

            $scope.list();
          });
        };

        /**
         * @function list
         * @memberOf NotificationListCtrl
         *
         * @description
         *   Reloads the list.
         */
        $scope.list = function () {
          $scope.loading = 1;

          var cleaned = itemService.cleanFilters($scope.criteria);

          var data = {
            criteria: cleaned,
            orderBy:  $scope.orderBy,
            epp:      $scope.pagination.epp,
            page:     $scope.pagination.page
          };

          itemService.encodeFilters($scope.criteria, $scope.orderBy,
            $scope.pagination.epp, $scope.pagination.page);

          itemService.list('manager_ws_notifications_list', data).then(
            function(response) {
              $scope.items            = response.data.results;
              $scope.pagination.total = response.data.total;
              $scope.extra            = response.data.extra;

              $scope.loading = 0;

              // Scroll top
              $('.page-content').animate({ scrollTop: '0px' }, 1000);
            }
          );
        };

        /**
         * @function resetFilters
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Resets all filters to the initial value.
         */
        $scope.resetFilters = function() {
          $scope.criteria = { title_like: [ { value: '', operator: 'like' } ]};
          $scope.orderBy  = [ { name: 'start', value: 'desc' } ];

          $scope.pagination.page = 1;
        };

        /**
         * @function setEnabled
         * @memberOf NotificationListCtrl
         *
         * @description
         *   Enables/disables an notification.
         *
         * @param boolean enabled Notification activated value.
         */
        $scope.setEnabled = function(notification, enabled) {
          notification.loading = 1;

          itemService.patch('manager_ws_notification_patch', notification.id,
            { activated: enabled }).success(function(response) {
              notification.loading = 0;
              notification.activated = enabled;

              messenger.post({ message: response, type: 'success' });
            }).error(function(response) {
              messenger.post({ message: response, type: 'error' });
            });
        };

        /**
         * @function setEnabledSelected
         * @memberOf NotificationListCtrl
         *
         * @description
         *   Enables/disables the selected notifications.
         *
         * @param integer enabled The activated value.
         */
        $scope.setEnabledSelected = function(enabled) {
          for (var i = 0; i < $scope.items.length; i++) {
            var id = $scope.items[i].id;
            if ($scope.selected.items.indexOf(id) !== -1) {
              $scope.items[i].loading = 1;
            }
          }

          var data = { selected: $scope.selected.items, activated: enabled };

          itemService.patchSelected('manager_ws_notifications_patch', data)
            .success(function(response) {
              // Update notifications changed successfully
              for (var i = 0; i < $scope.items.length; i++) {
                var id = $scope.items[i].id;

                if (response.success.indexOf(id) !== -1) {
                  $scope.items[i].activated = enabled;
                  delete $scope.items[i].loading;
                }
              }

              if (response.messages) {
                messenger.post(reponse.messages);

                $scope.selected = { all: false, items: [] };
              } else {
                messenger.post(response);
              }

              if (response.success.length > 0) {
                $scope.list();
              }
            }).error(function(response) {
              // Update notifications changed successfully
              for (var i = 0; i < $scope.items.length; i++) {
                delete $scope.items[i].loading;
              }

              if (response.messages) {
                messenger.post(response.messages);

                $scope.selected = { all: false, items: [] };
              } else {
                messenger.post(response);
              }
            });
        };

        // Updates the columns stored in localStorage.
        $scope.$watch('columns', function(newValues, oldValues) {
          if (newValues !== oldValues) {
            webStorage.local.add('notifications-columns', $scope.columns);
          }
        }, true);

        // Initialize filters from URL
        var filters = itemService.decodeFilters();
        for (var name in filters) {
          $scope[name] = filters[name];
        }

        // Get enabled columns from localStorage
        if (webStorage.local.get('notifications-columns')) {
          $scope.columns = webStorage.local.get('notifications-columns');
        }
      }
    ]);
})();
