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
     * @requires http
     * @requires messenger
     * @requires webStorage
     *
     * @description
     *   Handles all actions in notifications listing.
     */
    .controller('NotificationListCtrl', [
      '$controller', '$uibModal', '$location', '$scope', '$timeout', 'http', 'messenger', 'oqlEncoder', 'webStorage',
      function($controller, $uibModal, $location, $scope, $timeout, http, messenger, oqlEncoder, webStorage) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope:   $scope,
          $timeout: $timeout
        }));

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
            'title', 'l10n', 'start', 'end', 'fixed', 'forced', 'enabled'
          ]
        };

        /**
         * @memberOf NotificationListCtrl
         *
         * @description
         *   The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = { epp: 25, page: 1 };

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
         * @param {Integer} id The notification id.
         */
        $scope.delete = function(id) {
          var modal = $uibModal.open({
            templateUrl: '/managerws/template/notification:modal.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { };
              },
              success: function() {
                return function(modalWindow) {
                  var route = {
                    name: 'manager_ws_notification_delete',
                    params: { id: id }
                  };

                  http.delete(route).then(function(response) {
                    modalWindow.close({ data: response.data, success: true });
                  }, function(response) {
                    modalWindow.close({ data: response.data, type: false });
                  });
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.list();
            }
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
                return function(modalWindow) {
                  var route = 'manager_ws_notifications_delete';
                  var data  = { ids: $scope.selected.items };

                  http.delete(route, data).then(function(response) {
                    modalWindow.close({ data: response.data, success: true });
                  }, function(response) {
                    modalWindow.close({ data: response.data, success: false });
                  });
                };
              }
            }
          });

          modal.result.then(function(response) {
            messenger.post(response.data);

            if (response.success) {
              $scope.selected = { all: false, items: [] };
              $scope.list();
            }
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

          oqlEncoder.configure({
            placeholder: {
              title: 'title regexp \'"[^"]*[value][^"]*";\' or body regexp \'"[^"]*[value][^"]*";\''
            }
          });

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: 'manager_ws_notifications_list',
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            $scope.loading = 0;
            $scope.items   = response.data.results;
            $scope.total   = response.data.total;
            $scope.extra   = response.data.extra;

            // Scroll top
            $('body').animate({ scrollTop: '0px' }, 1000);
          });
        };

        /**
         * @function resetFilters
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Resets all filters to the initial value.
         */
        $scope.resetFilters = function() {
          $scope.criteria = { epp: 25, page: 1 };
        };

        /**
         * @function patch
         * @memberOf NotificationListCtrl
         *
         * @description
         *   Enables/disables an notification.
         *
         * @param {String}  notification The notification object.
         * @param {String}  property     The property name.
         * @param {Boolean} value        The property value.
         */
        $scope.patch = function(notification, property, value) {
          var data = {};

          notification[property + 'Loading'] = 1;
          data[property] = value;

          var route = {
            name: 'manager_ws_notification_patch',
            params: { id: notification.id }
          };

          http.patch(route, data).then(function(response) {
              notification[property + 'Loading'] = 0;
              notification[property] = value;
              messenger.post(response.data);
            }, function(response) {
              notification[property + 'Loading'] = 0;
              messenger.post(response.data);
            });
        };

        /**
         * @function patchSelected
         * @memberOf NotificationListCtrl
         *
         * @description
         *   Enables/disables the selected notifications.
         *
         * @param {String}  property The property name.
         * @param {Boolean} value    The property value.
         */
        $scope.patchSelected = function(property, value) {
          for (var i = 0; i < $scope.items.length; i++) {
            var id = $scope.items[i].id;
            if ($scope.selected.items.indexOf(id) !== -1) {
              $scope.items[i][property + 'Loading'] = 1;
            }
          }

          var data = { ids: $scope.selected.items };
          data[property] = value;

          http.patch('manager_ws_notifications_patch', data)
            .then(function(response) {
              $scope.list().then(function() {
                messenger.post(response.data);
                $scope.selected = { all: false, items: [] };
              });
            }, function(response) {
              $scope.list().then(function() {
                messenger.post(response.data);
                $scope.selected = { all: false, items: [] };
              });
            });
        };

        // Updates the columns stored in localStorage.
        $scope.$watch('columns', function(newValues, oldValues) {
          if (newValues !== oldValues) {
            webStorage.local.set('notifications-columns', $scope.columns);
          }
        }, true);

        // Get enabled columns from localStorage
        if (webStorage.local.get('notifications-columns')) {
          $scope.columns = webStorage.local.get('notifications-columns');
        }

        if (webStorage.local.get('token')) {
          $scope.token = webStorage.local.get('token');
        }

        $scope.list();
      }
    ]);
})();
