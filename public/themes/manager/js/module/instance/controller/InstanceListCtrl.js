(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  InstanceListCtrl
     *
     * @requires $controller
     * @requires $uibModal
     * @requires $location
     * @requires $scope
     * @requires $timeout
     * @requires http
     * @requires messenger
     * @requires oqlEncoder
     * @requires webStorage
     *
     * @description
     *   Handles all actions in instances list.
     */
    .controller('InstanceListCtrl', [
      '$controller', '$uibModal', '$location' ,'$scope', '$timeout', 'http', 'messenger', 'oqlDecoder', 'oqlEncoder', 'webStorage',
      function($controller, $uibModal, $location, $scope, $timeout, http, messenger, oqlDecoder, oqlEncoder, webStorage) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope:   $scope,
          $timeout: $timeout
        }));

        /**
         * @memberOf InstanceListCtrl
         *
         * @description
         *   The visible table columns.
         *
         * @type {Object}
         */
        $scope.columns = {
          collapsed: 1,
          selected: [
            'name', 'domains', 'last_login', 'created', 'articles', 'alexa',
            'activated'
          ]
        };

        /**
         * @memberOf InstanceListCtrl
         *
         * @description
         *   The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = { epp: 25, page: 1 };

        /**
         * @function delete
         * @memberOf InstanceListCtrl
         *
         * @description
         *   Confirm delete action.
         *
         * @param {Integer} id The instance id.
         */
        $scope.delete = function(id) {
          var modal = $uibModal.open({
            templateUrl: '/managerws/template/instance:modal.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { };
              },
              success: function() {
                return function(modalWindow) {
                  var route = {
                    name:   'manager_ws_instance_delete',
                    params: { id: id }
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
         * @memberOf InstanceListCtrl
         *
         * @description
         *   Confirm delete action.
         */
        $scope.deleteSelected = function() {
          var modal = $uibModal.open({
            templateUrl: '/managerws/template/instance:modal.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return { selected: $scope.selected.items.length };
              },
              success: function() {
                return function(modalWindow) {
                  var route = 'manager_ws_instances_delete';
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
         * @function getCountry
         * @memberOf InstanceCtrl
         *
         * @description
         *   Returns the country given a code.
         *
         * @param {String} code The country code.
         *
         * @return {Object} The country object.
         */
        $scope.getCountry = function(code) {
          if (!code) {
            return null;
          }

          var countries = $scope.extra.countries.filter(function(e) {
            return e.id === code;
          });

          if (countries.length === 0) {
            return '';
          }

          return countries[0].name;
        };

        /**
         * @function list
         * @memberOf InstanceListCtrl
         *
         * @description
         *   Reloads the list.
         */
        $scope.list = function () {
          $scope.loading = 1;

          oqlEncoder.configure({
            placeholder: {
              name: 'name ~ "[value]" or ' +
                'internal_name ~ "[value]" or ' +
                'contact_mail ~ "[value]" or ' +
                'domains ~ "[value]" or settings ~ "[value]"'
            }
          });

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: 'manager_ws_instances_list',
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
         * @memberOf InstanceListCtrl
         *
         * @description
         *   Enables/disables an instance.
         *
         * @param {String}  item     The notification object.
         * @param {String}  property The property name.
         * @param {Boolean} value    The property value.
         */
        $scope.patch = function(item, property, value) {
          var data = {};

          item[property + 'Loading'] = 1;
          data[property] = value;

          var route = {
            name:   'manager_ws_instance_patch',
            params: { id: item.id }
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
         * @memberOf ModuleListCtrl
         *
         * @description
         *   Enables/disables the selected modules.
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

          http.patch('manager_ws_instances_patch', data)
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
            webStorage.local.set('instances-columns', $scope.columns);
          }
        }, true);

        // Get enabled columns from localStorage
        if (webStorage.local.get('instances-columns')) {
          $scope.columns = webStorage.local.get('instances-columns');
        }

        if ($location.search().oql) {
          $scope.criteria = oqlDecoder.decode($location.search().oql);
        }

        oqlDecoder.configure({
          ignore: [ 'internal_name', 'contact_mail', 'domains', 'settings' ]
        });

        if ($location.search().oql) {
          $scope.criteria = oqlDecoder.decode($location.search().oql);
        }

        $scope.list();
      }
    ]);
})();
