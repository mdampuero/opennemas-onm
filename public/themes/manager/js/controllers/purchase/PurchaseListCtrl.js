(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  PurchaseListCtrl
     *
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
      '$modal', '$scope', '$timeout', 'itemService', 'routing', 'messenger', 'webStorage', 'data',
      function($modal, $scope, $timeout, itemService, routing, messenger, webStorage, data) {
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
          selected: [
            'name', 'domains', 'last_login', 'created',
            'articles', 'alexa', 'activated'
          ]
        };

        /**
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   The list of elements.
         *
         * @type {Object}
         */
        $scope.items = data.results;

        $scope.extra = data.extra;

        /**
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   The list of selected elements.
         *
         * @type {Array}
         */
        $scope.selected = { all: false, items: [] };

        /**
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   The listing order.
         *
         * @type {Object}
         */
        $scope.orderBy = [{ name: 'created', value: 'desc' }];

        /**
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   The current pagination status.
         *
         * @type {Object}
         */
        $scope.pagination = {
          epp: data.epp ? parseInt(data.epp) : 25,
          page: data.page ? parseInt(data.page) : 1,
          total: data.total
        };

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
         * @function isEnabled
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Checks if a columns is selected.
         *
         * @param {String} id The columns name.
         */
        $scope.isEnabled = function(id) {
          return $scope.columns.selected.indexOf(id) !== -1;
        };

        /**
         * @function isOrderedBy
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Checks if the listing is ordered by the given field name.
         *
         * @param string name The field name.
         *
         * @return mixed The order value, if the order exists. Otherwise,
         *               returns false.
         */
        $scope.isOrderedBy = function(name) {
          var i = 0;
          while (i < $scope.orderBy.length && $scope.orderBy[i].name !== name) {
            i++;
          }

          if (i < $scope.orderBy.length) {
            return $scope.orderBy[i].value;
          }

          return false;
        };

        /**
         * @function isSelected
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Checks if an purchase is selected.
         *
         * @param string id The group id.
         */
        $scope.isSelected = function(id) {
          return $scope.selected.items.indexOf(id) !== -1;
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
            list();
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

            list();
          });
        };

        /**
         * @function isEnabled
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Reloads the listing.
         */
        $scope.refresh = function() {
          list();
        };

        $scope.resetFilters = function() {
          $scope.criteria   = { name_like: [ { value: '', operator: 'like' } ] };
          $scope.orderBy    = [ { name: 'created', value: 'desc' } ];

          $scope.pagination.page = 1;

          $scope.refresh();
        }

        /**
         * @function isEnabled
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Reloads the list on keypress.
         *
         * @param  Object event The even object.
         */
        $scope.searchByKeypress = function(event) {
          if (event.keyCode === 13) {
            if ($scope.pagination.page !== 1) {
              $scope.pagination.page = 1;
            } else {
              list();
            }
          }
        };

        /**
         * @function isEnabled
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Selects/unselects all purchases.
         */
        $scope.selectAll = function() {
          if ($scope.selected.all) {
            $scope.selected.items = $scope.items.map(function(purchase) {
              return purchase.id;
            });
          } else {
            $scope.selected.items = [];
          }
        };

        /**
         * @function isEnabled
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
         * @function isEnabled
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

          var data = { selected: $scope.selected.items, activated: enabled };

          itemService.patchSelected('manager_ws_purchases_patch', data)
            .success(function(response) {
              // Update purchases changed successfully
              for (var i = 0; i < $scope.items.length; i++) {
                var id = $scope.items[i].id;

                if (response.success.indexOf(id) !== -1) {
                  $scope.items[i].activated = enabled;
                  delete $scope.items[i].loading;
                }
              }

              if (response.messages) {
                // TODO: Remove when merging feature/ONM-352
                for (var i = 0; i < response.messages.length; i++) {
                  messenger.post(response.messages[i]);
                }

                $scope.selected = { all: false, items: [] };
              } else {
                messenger.post(response);
              }

              if (response.success.length > 0) {
                list();
              }
            }).error(function(response) {
              // Update purchases changed successfully
              for (var i = 0; i < $scope.items.length; i++) {
                delete $scope.items[i].loading;
              }

              if (response.messages) {
                // TODO: Remove when merging feature/ONM-352
                for (var i = 0; i < response.messages.length; i++) {
                  messenger.post(response.messages[i]);
                }

                $scope.selected = { all: false, items: [] };
              } else {
                messenger.post(response);
              }
            });
        };

        /**
         * @function isEnabled
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Changes the sort order.
         *
         * @param string name Field name.
         */
        $scope.sort = function(name) {
          var i = 0;
          while (i < $scope.orderBy.length && $scope.orderBy[i].name !== name) {
            i++;
          }

          if (i >= $scope.orderBy.length) {
            $scope.orderBy.push({
              name: name,
              value: 'asc'
            });
          } else {
            if ($scope.orderBy[i].value === 'asc') {
              $scope.orderBy[i].value = 'desc';
            } else {
              $scope.orderBy.splice(i, 1);
            }
          }

          $scope.pagination.page = 1;
        };

        /**
         * @function isEnabled
         * @memberOf PurchaseListCtrl
         *
         * @description
         *   Toggles column filters container.
         */
        $scope.toggleColumns = function() {
          $scope.columns.collapsed = !$scope.columns.collapsed;

          if (!$scope.columns.collapsed) {
            $scope.scrollTop();
          }
        };

        // Marks variables to delete for garbage collector
        $scope.$on('$destroy', function() {
          $scope.criteria = null;
          $scope.columns = null;
          $scope.pagination.epp = null;
          $scope.items = null;
          $scope.selected = null;
          $scope.orderBy = null;
          $scope.pagination.page = null;
          $scope.pagination.total = null;
        });

        // Refresh the list of elements when some parameter changes
        $scope.$watch('[orderBy, pagination.epp, pagination.page]', function(newValues, oldValues) {
          if (newValues !== oldValues) {
            list();
          }
        }, true);

        // Updates the columns stored in localStorage.
        $scope.$watch('columns', function(newValues, oldValues) {
          if (newValues !== oldValues) {
            webStorage.local.add('purchases-columns', $scope.columns);
          }
        }, true);

        /**
         * Searches purchases given a criteria.
         *
         * @return Object The function to execute past 500 ms.
         */
        function list() {
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
        }

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

