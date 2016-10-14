(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  PurchaseListCtrl
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
     *   Handles all actions in purchases listing.
     */
    .controller('PurchaseListCtrl', [
      '$controller', '$location', '$scope', '$timeout', '$uibModal', 'http', 'messenger', 'oqlEncoder', 'oqlDecoder', 'webStorage',
      function($controller, $location, $scope, $timeout, $uibModal, http, messenger, oqlEncoder, oqlDecoder, webStorage) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope:   $scope,
          $timeout: $timeout
        }));

        /**
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   The visible table columns.
         *
         * @type {Object}
         */
        $scope.columns = {
          collapsed: 1,
          selected: [ 'name', 'client_id', 'payment_id', 'invoice_id', 'updated', 'step' ]
        };

        /**
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = { epp: 25, orderBy: { updated: 'desc' }, page: 1 };

        /**
         * @function delete
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Confirm delete action.
         *
         * @param {Integer} id The purchase id.
         */
        $scope.delete = function(id) {
          var modal = $uibModal.open({
            templateUrl: '/managerws/template/purchase:modal.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { };
              },
              success: function() {
                return function(modalWindow) {
                  var route = {
                    name: 'manager_ws_purchase_delete',
                    params: { id: id }
                  };

                  http.delete(route).then(function(response) {
                    modalWindow.close({ data: response.data, success: true });
                  }, function(response) {
                    modalWindow.close({ message: response.data, success: false });
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
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Confirm delete action.
         */
        $scope.deleteSelected = function() {
          var modal = $uibModal.open({
            templateUrl: '/managerws/template/purchase:modal.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { selected: $scope.selected.items.length };
              },
              success: function() {
                return function(modalWindow) {
                  var route = 'manager_ws_purchases_delete';
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
         * @function refresh
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Reloads the list.
         */
        $scope.list = function () {
          $scope.loading = 1;

          oqlEncoder.configure({
            placeholder: {
              client: 'client ~ "[value]"',
              from: 'created > "[value]"',
              to: 'created < "[value]"'
            }
          });

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: 'manager_ws_purchases_list',
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

        // Updates the columns stored in localStorage.
        $scope.$watch('columns', function(newValues, oldValues) {
          if (newValues !== oldValues) {
            webStorage.local.set('purchases-columns', $scope.columns);
          }
        }, true);

        // Get enabled columns from localStorage
        if (webStorage.local.get('purchases-columns')) {
          $scope.columns = webStorage.local.get('purchases-columns');
        }

        if ($location.search().oql) {
          $scope.criteria = oqlDecoder.decode($location.search().oql);
        }

        $scope.list();
      }
    ]);
})();
