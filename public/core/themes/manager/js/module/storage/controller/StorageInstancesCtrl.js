(function() {
  'use strict';

  angular.module('ManagerApp.controllers')
    .controller('StorageInstancesCtrl', [
      '$controller', '$uibModal', '$location', '$scope', '$timeout', 'http', 'messenger', 'oqlDecoder', 'oqlEncoder', 'webStorage',
      function($controller, $uibModal, $location, $scope, $timeout, http, messenger, oqlDecoder, oqlEncoder, webStorage) {
        function ensureProvider(provider, createDefault) {
          if (!provider) {
            if (createDefault === false) {
              return null;
            }
            provider = {};
          }

          if (!provider.type) {
            var hasBunnyFields = provider.api_base_url || provider.embed_base_url ||
              provider.library_id || provider.api_key;

            provider.type = hasBunnyFields ? 'bunny' : 's3';
          }

          return provider;
        }

        $.extend(this, $controller('ListCtrl', {
          $scope: $scope,
          $timeout: $timeout
        }));

        $scope.isBunnyProvider = function(provider) {
          provider = ensureProvider(provider, false);
          return provider ? provider.type === 'bunny' : false;
        };

        $scope.isS3Provider = function(provider) {
          return !$scope.isBunnyProvider(provider);
        };

        $scope.getProviderField = function(provider, field) {
          provider = ensureProvider(provider, false);

          if (!provider) {
            return '';
          }
          if (provider.type === 'bunny') {
            switch (field) {
              case 'endpoint':
                return provider.api_base_url || '';
              case 'bucket':
                return provider.library_id || '';
              case 'public_endpoint':
                return provider.embed_base_url || '';
              default:
                return provider[field] || '';
            }
          }

          return provider && provider[field] ? provider[field] : '';
        };

        $scope.columns = {
          collapsed: 1,
          selected: [
            'name',
            'manager_config'
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
            $scope.items = response.data.results.map(function(item) {
              item.storage_settings = item.storage_settings || {};
              item.storage_settings.provider = ensureProvider(item.storage_settings.provider, false);
              return item;
            });
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
                var settings = angular.copy(item.storage_settings) || { };

                settings.provider = ensureProvider(settings.provider, true);

                return {
                  storage_settings: settings
                };
              },
              success: function() {
                return function(modalWindow, template) {
                  template.storage_settings = template.storage_settings || {};
                  template.storage_settings.provider = ensureProvider(template.storage_settings.provider, true);

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

        $scope.useManagerConfig = function(item) {
          var modal = $uibModal.open({
            templateUrl: '/managerws/template/storage:modalUseManagerConfig.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {};
              },
              success: function() {
                return function(modalWindow) {
                  var route = {
                    name: 'manager_ws_storage_instances_save',
                    params: {
                      id: item.id,
                      storage_settings: []
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
            messenger.post(response.data);

            if (response.success) {
              $scope.list();
            }
          });
        };

        $scope.list();
      }
    ]);
})();
