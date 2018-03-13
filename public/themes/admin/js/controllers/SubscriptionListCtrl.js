(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SubscriptionListCtrl
     *
     * @requires $controller
     * @requires $location
     * @requires $scope
     * @requires $timeout
     * @requires $uibModal
     * @requires http
     * @requires messenger
     * @requires oqlEncoder
     * @requires webStorage
     *
     * @description
     *   Handles all actions in subscriptions list.
     */
    .controller('SubscriptionListCtrl', [
      '$controller', '$location', '$scope', '$timeout', '$uibModal', 'http', 'messenger', 'oqlEncoder', 'webStorage',
      function($controller, $location, $scope, $timeout, $uibModal, http, messenger, oqlEncoder, webStorage) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope:   $scope,
          $timeout: $timeout
        }));

        /**
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *   The visible table columns.
         *
         * @type {Object}
         */
        $scope.columns = {
          collapsed: 1,
          selected:  [ 'name' ]
        };

        /**
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *   The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          epp: 25,
          page: 1,
          orderBy: { name: 'asc' }
        };

        /**
         * @function delete
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *   Confirm delete action.
         *
         * @param {Integer} id The group id.
         */
        $scope.delete = function(id) {
          var modal = $uibModal.open({
            templateUrl: 'modal-delete',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { content: $scope.items.filter(function(e) {
                  return e.pk_user_group === id;
                })[0] };
              },
              success: function() {
                return function(modalWindow) {
                  var route = {
                    name: 'api_v1_backend_subscription_delete',
                    params: { id: id }
                  };

                  return http.delete(route).then(function(response) {
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
              $scope.list();
            }
          });
        };

        /**
         * @function deleteSelected
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *   Confirm delete action.
         */
        $scope.deleteSelected = function() {
          var modal = $uibModal.open({
            templateUrl: 'modal-delete-selected',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { selected: $scope.selected.items.length };
              },
              success: function() {
                return function(modalWindow) {
                  var route = 'api_v1_backend_subscriptions_delete';
                  var data  = { ids: $scope.selected.items };

                  return http.delete(route, data).then(function(response) {
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
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *   Reloads the list.
         */
        $scope.list = function() {
          $scope.flags.loading = 1;

          oqlEncoder.configure({ placeholder: { name: '[key] ~ "[value]"' } });

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: 'api_v1_backend_subscriptions_list',
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            $scope.data  = response.data;
            $scope.items = response.data.results;

            $scope.disableFlags();

            // Scroll top
            $('body').animate({ scrollTop: '0px' }, 1000);
          });
        };

        /**
         * @function patch
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *   Enables/disables a subscription.
         *
         * @param {String}  item     The subscription object.
         * @param {String}  property The property name.
         * @param {Boolean} value    The property value.
         */
        $scope.patch = function(item, property, value) {
          var data = {};

          item[property + 'Loading'] = 1;
          data[property] = value;

          var route = {
            name:   'api_v1_backend_subscription_patch',
            params: { id: item.pk_user_group }
          };

          http.patch(route, data).then(function(response) {
            item[property + 'Loading'] = 0;
            item[property] = value;
            messenger.post(response.data);
          }, function(response) {
            item[property + 'Loading'] = 0;
            messenger.post(response.data);
          });
        };

        /**
         * @function patchSelected
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *   description
         *
         * @param {String}  property The property name.
         * @param {Integer} value    The property value.
         */
        $scope.patchSelected = function(property, value) {
          for (var i = 0; i < $scope.items.length; i++) {
            var id = $scope.items[i].pk_user_group;

            if ($scope.selected.items.indexOf(id) !== -1) {
              $scope.items[i][property + 'Loading'] = 1;
            }
          }

          var data = { ids: $scope.selected.items };

          data[property] = value;

          http.patch('api_v1_backend_subscriptions_patch', data).then(function(response) {
            $scope.list().then(function() {
              $scope.selected = { all: false, items: [] };
              messenger.post(response.data);
            });
          }, function(response) {
            $scope.list().then(function() {
              $scope.selected = { all: false, items: [] };
              messenger.post(response.data);
            });
          });
        };

        /**
         * @function resetFilters
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *   Resets all filters to the initial value.
         */
        $scope.resetFilters = function() {
          $scope.criteria = { epp: 25, page: 1 };
        };

        /**
         * @function toggleAll
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *   Toggles all items selection.
         */
        $scope.toggleAll = function() {
          if ($scope.selected.all) {
            $scope.selected.items = $scope.items.map(function(item) {
              return item.pk_user_group;
            });
          } else {
            $scope.selected.items = [];
          }
        };

        // Updates the columns stored in localStorage.
        $scope.$watch('columns', function(nv, ov) {
          if (nv !== ov) {
            webStorage.local.set('subscriptions-columns', nv);
          }
        }, true);

        // Get enabled columns from localStorage
        if (webStorage.local.get('subscriptions-columns')) {
          $scope.columns = webStorage.local.get('subscriptions-columns');
        }
      }
    ]);
})();
