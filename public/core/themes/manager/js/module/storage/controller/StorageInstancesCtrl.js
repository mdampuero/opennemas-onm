(function() {
  'use strict';

  angular.module('ManagerApp.controllers')
    .controller('StorageInstancesCtrl', [
      '$controller', '$uibModal', '$location', '$scope', '$timeout', 'http', 'messenger', 'oqlDecoder', 'oqlEncoder', 'webStorage',
      function($controller, $uibModal, $location, $scope, $timeout, http, messenger, oqlDecoder, oqlEncoder, webStorage) {
        $.extend(this, $controller('ListCtrl', {
          $scope: $scope,
          $timeout: $timeout
        }));

        $scope.columns = {
          collapsed: 1,
          selected: [
            'name'
          ]
        };

        $scope.criteria = {
          epp: 25,
          page: 1,
          activated_modules: 'es.openhost.module.storage'
        };

        $scope.list = function() {
          $scope.loading = 1;

          oqlEncoder.configure({
            placeholder: {
              name: 'name ~ "[value]" or ' +
                'internal_name ~ "[value]" or ' +
                'contact_mail ~ "[value]" or ' +
                'domains ~ "[value]" or ' +
                'settings ~ "[value]"',
              activated_modules: '[key] ~ "[value]"'
            }
          });

          var oql = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: 'manager_ws_storage_instances',
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            $scope.loading = 0;
            $scope.items = response.data.results;
            $scope.total = response.data.total;
            $scope.extra = response.data.extra;

            $scope.filteredItems = $scope.items;

            $('body').animate({ scrollTop: '0px' }, 1000);
          });
        };

        $scope.resetFilters = function() {
          $scope.criteria = { epp: 25, page: 1, activated_modules: 'es.openhost.module.storage' };
          $scope.list();
        };

        $scope.$watch('columns', function(newValues, oldValues) {
          if (newValues !== oldValues) {
            webStorage.local.set('storage-instances-columns', $scope.columns);
          }
        }, true);

        if (webStorage.local.get('storage-instances-columns')) {
          $scope.columns = webStorage.local.get('storage-instances-columns');
        }

        oqlDecoder.configure({
          ignore: ['internal_name', 'contact_mail', 'domains', 'settings']
        });

        $scope.openStorageSettings = function(item) {
          var modal = $uibModal.open({
            templateUrl: '/managerws/template/storage:modalStorageSettings.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  storage_settings: angular.copy(item.storage_settings)
                };
              },
              success: function() {
                return function(modalWindow, template) {
                  var route = {
                    name: 'manager_ws_storage_instances_save',
                    params: {
                      id: item.id,
                      storage_settings: {
                        compress: template.storage_settings && template.storage_settings.compress || null,
                        thumbnail: template.storage_settings && template.storage_settings.thumbnail || null,
                        tasks: template.storage_settings && template.storage_settings.tasks || [],
                        provider: template.storage_settings && template.storage_settings.provider || null
                      }
                    }
                  };

                  http.put(route).then(function(response) {
                    modalWindow.close({ data: response.data, success: true });
                  }, function(response) {
                    modalWindow.close({ data: response.data, success: false });
                  });
                };
              }
            }
          });

          modal.result.then(function(response) {
            if (response && response.data) {
              messenger.post(response.data);
              if (response.success) {
                $scope.list();
              }
            }
          });
        };

        $scope.list();
      }
    ]);
})();
