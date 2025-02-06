(function() {
  'use strict';

  angular.module('ManagerApp.controllers')

    /**
     * @ngdoc controller
     * @name  OnmAiPromptListCtrl
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
     *   Handles all actions in prompt.txt listing.
     */
    .controller('OnmAiPromptListCtrl', [
      '$controller', '$location', '$scope', '$timeout', '$uibModal', 'http', 'messenger', 'oqlDecoder', 'oqlEncoder', 'webStorage',
      function($controller, $location, $scope, $timeout, $uibModal, http, messenger, oqlDecoder, oqlEncoder, webStorage) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope:   $scope,
          $timeout: $timeout
        }));

        /**
         * @memberOf OnmAiPromptListCtrl
         *
         * @description
         *   The visible table columns.
         *
         * @type {Object}
         */
        $scope.columns = {
          collapsed: 1,
          selected: [ 'name', 'instances' ]
        };

        /**
         * @memberOf OnmAiPromptListCtrl
         *
         * @description
         *   The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = { epp: 25, page: 1 };

        /**
         * @function delete
         * @memberOf OnmAiPromptListCtrl
         *
         * @description
         *   Confirm delete action.
         *
         * @param {Object item The item to delete.
         */
        $scope.delete = function(item) {
          var modal = $uibModal.open({
            templateUrl: '/managerws/template/onmai:prompt:modal.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { };
              },
              success: function() {
                return function(modalWindow) {
                  var route = {
                    name: 'manager_ws_onmai_prompt_delete',
                    params: { id: item.id }
                  };

                  http.delete(route).then(function(response) {
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
         * @memberOf OnmAiPromptListCtrl
         *
         * @description
         *   Confirm delete action.
         */
        $scope.deleteSelected = function() {
          var modal = $uibModal.open({
            templateUrl: '/managerws/template/onmai:prompt:modal.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { selected: $scope.selected.items.length };
              },
              success: function() {
                return function(modalWindow) {
                  var route = 'manager_ws_onmai_prompt_batch_delete';
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
         * @memberOf OnmAiPromptListCtrl
         *
         * @description
         *   Reloprompt the list.
         */
        $scope.list = function() {
          $scope.loading = 1;

          oqlEncoder.configure({
            placeholder: {
              name: 'name ~ "[value]" or instances ~ "[value]"',
            }
          });

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: 'manager_ws_onmai_prompt_list',
            params: { oql: oql }
          };

          $location.search('oql', oql);

          http.get(route).then(function(response) {
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
         * @memberOf OnmAiPromptListCtrl
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
            webStorage.local.set('prompt-columns', $scope.columns);
          }
        }, true);

        // Get enabled columns from localStorage
        if (webStorage.local.get('prompt-columns')) {
          $scope.columns = webStorage.local.get('prompt-columns');
        }

        oqlDecoder.configure({
          ignore: [ 'prompt_lines', 'instances' ],
          map:    { name: 'name' }
        });

        if ($location.search().oql) {
          $scope.criteria = oqlDecoder.decode($location.search().oql);
        }

        $scope.list();
      }
    ]);
})();

