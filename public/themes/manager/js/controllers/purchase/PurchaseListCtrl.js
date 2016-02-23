(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  PurchaseListCtrl
     *
     * @requires $controller
     * @requires $modal
     * @requires $scope
     * @requires itemService
     * @requires routing
     * @requires messenger
     * @requires webStorage
     * @requires data
     *
     * @description
     *   Handles all actions in purchases listing.
     */
    .controller('PurchaseListCtrl', [
      '$controller', '$modal', '$scope', '$timeout', 'itemService', 'routing', 'messenger', 'webStorage', 'data',
      function($controller, $modal, $scope, $timeout, itemService, routing, messenger, webStorage, data) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope:   $scope,
          $timeout: $timeout,
          data:     data
        }));

        /**
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          name_like: []
        };

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
          selected: [ 'name', 'client_id', 'payment_id', 'invoice_id', 'created' ]
        };

        /**
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   The listing order.
         *
         * @type {Object}
         */
        $scope.orderBy = [{ name: 'created', value: 'desc' }];

        var e = null;

        /**
         * @function closeColumns
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Hides the dropdown to toggle table columns.
         */
        $scope.closeColumns = function() {
          if (e) {
            $timeout.cancel(e);
          }

          e = $timeout(function () {
            $scope.open = false;
          }, 500);
        };

        /**
         * @function openColumns
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Shows the dropdown to toggle table columns.
         */
        $scope.openColumns = function() {
          if (e) {
            $timeout.cancel(e);
          }

          e = $timeout(function () {
            $scope.open = true;
          }, 500);
        };

        /**
         * @function delete
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Confirm delete action.
         *
         * @param {Object purchase The purchase to delete.
         */
        $scope.delete = function(purchase) {
          var modal = $modal.open({
            templateUrl: 'modal-confirm',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  name: 'delete-purchase',
                  item: purchase
                };
              },
              success: function() {
                return function(modalPurchase) {
                  itemService.delete('manager_ws_purchase_delete', purchase.id)
                    .success(function(response) {
                      modalPurchase.close({ message: response, type: 'success'});
                    }).error(function(response) {
                      modalPurchase.close({ message: response, type: 'error'});
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
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Confirm delete action.
         */
        $scope.deleteSelected = function() {
          var modal = $modal.open({
            templateUrl: 'modal-confirm',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                var selected = [];

                for (var i = 0; i < $scope.items.length; i++) {
                  if ($scope.selected.items.indexOf(
                    $scope.items[i].id) !== -1) {
                    selected.push($scope.items[i]);
                  }
                }

                return {
                  name: 'delete-purchases',
                  selected: selected
                };
              },
              success: function() {
                return function(modalPurchase) {
                  itemService.deleteSelected('manager_ws_purchases_delete',
                    $scope.selected.items).success(function(response) {
                      modalPurchase.close(response);
                    }).error(function(response) {
                      modalPurchase.close(response);
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
         * @function refresh
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Reloads the list.
         */
        $scope.list = function () {
          $scope.loading = 1;

          // Search by name, domains and contact mail
          if ($scope.criteria.name_like) {
            $scope.criteria.domains_like =
              $scope.criteria.contact_mail_like =
              $scope.criteria.name_like;
          }

          var cleaned = itemService.cleanFilters($scope.criteria);

          var data = {
            criteria: cleaned,
            orderBy: $scope.orderBy,
            epp: $scope.pagination.epp, // elements per page
            page: $scope.pagination.page
          };

          itemService.encodeFilters($scope.criteria, $scope.orderBy,
            $scope.pagination.epp, $scope.pagination.page);

          itemService.list('manager_ws_purchases_list', data).then(
            function(response) {
              $scope.items = response.data.results;
              $scope.pagination.total = response.data.total;
              $scope.extra = response.data.extra;

              $scope.loading = 0;

              // Scroll top
              $('.page-content').animate({ scrollTop: '0px' }, 1000);
            }
          );
        };

        $scope.resetFilters = function() {
          $scope.criteria   = { name_like: [ { value: '', operator: 'like' } ] };
          $scope.orderBy    = [ { name: 'created', value: 'desc' } ];

          $scope.pagination.page = 1;

          $scope.list();
        }

        /**
         * @function setEnabled
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Enables/disables an purchase.
         *
         * @param boolean enabled Purchase activated value.
         */
        $scope.setEnabled = function(purchase, enabled) {
          purchase.loading = 1;

          itemService.patch('manager_ws_purchase_patch', purchase.id,
            { activated: enabled }).success(function(response) {
              purchase.loading = 0;
              purchase.activated = enabled;

              messenger.post({ message: response, type: 'success' });
            }).error(function(response) {
              messenger.post({ message: response, type: 'error' });
            });
        };

        /**
         * @function setEnabledSelected
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Enables/disables the selected purchases.
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

          var data = { selected: $scope.selected.items, enabled: enabled };

          itemService.patchSelected('manager_ws_purchases_patch', data)
            .success(function(response) {
              // Update purchases changed successfully
              for (var i = 0; i < $scope.items.length; i++) {
                var id = $scope.items[i].id;

                if (response.success.indexOf(id) !== -1) {
                  $scope.items[i].enabled = enabled;
                  delete $scope.items[i].loading;
                }
              }

              if (response.messages) {
                messenger.post(response.messages);

                $scope.selected = { all: false, items: [] };
              } else {
                messenger.post(response);
              }

              if (response.success.length > 0) {
                $scope.list();
              }
            }).error(function(response) {
              // Update purchases changed successfully
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
            webStorage.local.add('purchases-columns', $scope.columns);
          }
        }, true);

        // Initialize filters from URL
        var filters = itemService.decodeFilters();
        for (var name in filters) {
          $scope[name] = filters[name];
        }

        // Get enabled columns from localStorage
        if (webStorage.local.get('purchases-columns')) {
          $scope.columns = webStorage.local.get('purchases-columns');
        }

        if (webStorage.local.get('token')) {
          $scope.token = webStorage.local.get('token');
        }

        // Prevent dropdown from closing
        $('.dropdown-menu').on('click', '.checkbox,label', function(e) {
          e.stopPropagation();
        })
      }
    ]);
})();

