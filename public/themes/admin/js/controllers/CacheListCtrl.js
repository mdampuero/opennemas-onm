(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  CacheListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $uibModal
     * @requires http
     *
     * @description
     *   Controller for cache list.
     */
    .controller('CacheListCtrl', [
      '$controller', '$scope', '$uibModal', 'http', 'messenger',
      function($controller, $scope, $uibModal, http, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @memberOf CacheListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_cache_delete_item',
          deleteList: 'api_v1_backend_cache_delete_list',
          getItem:    'api_v1_backend_cache_get_item'
        };

        /**
         * Removes a key or a list of keys from the cache service.
         *
         * @param {String} service The service name.
         * @param {String} id      The key or pattern to delete.
         */
        $scope.deleteItem = function(service, id) {
          $scope.flags.http.saving = true;

          var route = {
            name: $scope.routes.deleteItem,
            params: { service: service, id: id }
          };

          http.delete(route).then(function(response) {
            $scope.disableFlags('http');
            messenger.post(response.data);
          }, function(response) {
            $scope.disableFlags('http');
            messenger.post(response.data);
          });
        };

        /**
         * Removes all keys from the cache service.
         *
         * @param {String} service The service name.
         */
        $scope.deleteList = function(service) {
          $scope.flags.http.saving = true;

          var route = {
            name: $scope.routes.deleteList,
            params: { service: service }
          };

          http.delete(route).then(function(response) {
            $scope.disableFlags('http');
            messenger.post(response.data);
          }, function(response) {
            $scope.disableFlags('http');
            messenger.post(response.data);
          });
        };

        /**
         * Gets a value by cache id from the cache service.
         *
         * @param {String} service The service name.
         * @param {String} id      The cache id.
         */
        $scope.getItem = function(service, id) {
          $scope.flags.http.preview = true;

          var route = {
            name: $scope.routes.getItem,
            params: { service: service, id: id }
          };

          http.get(route).then(function(response) {
            $uibModal.open({
              templateUrl: 'modal-preview',
              backdrop: 'static',
              controller: 'ModalCtrl',
              resolve: {
                template: function() {
                  return {
                    value: JSON.parse(response.data.item)
                  };
                },
                success: function() {
                  return null;
                }
              }
            });
          }, function(response) {
            $scope.disableFlags('http');
            messenger.post(response.data);
          });
        };
      }
    ]);
})();
