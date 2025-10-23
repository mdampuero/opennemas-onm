(function() {
  'use strict';

  angular.module('ManagerApp.controllers')

    /**
     * @ngdoc controller
     * @name  StorageConfigCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $timeout
     * @requires http
     * @requires messenger
     *
     * @description
     *   Handles all actions in prompt.txt listing.
     */
    .controller('StorageConfigCtrl', [
      '$controller', '$scope', '$timeout', 'http', 'messenger', '$uibModal',
      function($controller, $scope, $timeout, http, messenger, $uibModal) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope: $scope,
          $timeout: $timeout
        }));

        function ensureProvider(provider) {
          provider = provider || {};

          if (!provider.type) {
            var hasBunnyFields = provider.api_base_url || provider.embed_base_url ||
              provider.library_id || provider.api_key;

            provider.type = hasBunnyFields ? 'bunny' : 's3';
          }

          return provider;
        }

        $scope.isBunnyProvider = function() {
          return $scope.storage_settings && $scope.storage_settings.provider &&
            $scope.storage_settings.provider.type === 'bunny';
        };

        $scope.isS3Provider = function() {
          return !$scope.isBunnyProvider();
        };

        $scope.save = function() {
          if ($scope.validateForm() === false) {
            return;
          }

          $scope.storage_settings.provider = ensureProvider($scope.storage_settings.provider);

          http.put('manager_ws_storage_config_save', {
            storage_settings: $scope.storage_settings
          })
            .then(function(response) {
              messenger.post(response.data);
            }, function(response) {
              messenger.post(response.data);
            });
        };

        $scope.validateForm = function() {
          var compress = $scope.storage_settings && $scope.storage_settings.compress;
          var provider = $scope.storage_settings && ensureProvider($scope.storage_settings.provider);

          if (compress && compress.enabled && !compress.command) {
            messenger.post('You must specify a command for video compression', 'error');
            return false;
          }

          if (provider) {
            if (provider.type === 'bunny') {
              if (!provider.api_base_url || !provider.embed_base_url ||
                !provider.library_id || !provider.api_key) {
                messenger.post('Complete all fields for Bunny Stream', 'error');
                return false;
              }
            } else if (provider.enabled && (
              !provider.endpoint ||
              !provider.key ||
              !provider.secret ||
              !provider.region ||
              !provider.public_endpoint)) {
              messenger.post('Complete all fields for S3 provider', 'error');
              return false;
            }
          }
          return true;
        };

        $scope.init = function() {
          $scope.loading = 1;
          var route = {
            name: 'manager_ws_storage_config'
          };

          http.get(route).then(function(response) {
            $scope.storage_settings                  = response.data.storage_settings;
            $scope.storage_settings.compress         = $scope.storage_settings.compress || {};
            $scope.storage_settings.compress.enabled = $scope.storage_settings.compress.enabled === 'true';
            $scope.storage_settings.provider         = ensureProvider($scope.storage_settings.provider);
            $scope.storage_settings.provider.enabled = $scope.storage_settings.provider.enabled === 'true';
            $scope.loading = 0;
          });
        };
        $scope.init();
      }
    ]);
})();

