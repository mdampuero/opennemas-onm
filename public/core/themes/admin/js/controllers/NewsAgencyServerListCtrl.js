(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  NewsAgencyServerListCtrl
     *
     * @requires $controller
     * @requires $http
     * @requires $scope
     * @requires messenger
     *
     * @description
     *   Controller for server list in news agency.
     */
    .controller('NewsAgencyServerListCtrl', [
      '$controller', '$scope', '$uibModal', 'http', 'messenger',
      function($controller, $scope, $uibModal, http, messenger) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @memberOf NewsAgencyServerListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem:      'api_v1_backend_news_agency_server_delete_item',
          deleteList:      'api_v1_backend_news_agency_server_delete_list',
          emptyItem:       'api_v1_backend_news_agency_server_empty_item',
          getList:         'api_v1_backend_news_agency_server_get_list',
          patchItem:       'api_v1_backend_news_agency_server_patch_item',
          patchList:       'api_v1_backend_news_agency_server_patch_list',
          redirect:        'backend_news_agency_server_show',
          synchronizeItem: 'api_v1_backend_news_agency_server_synchronize_item',
          updateItem:      'api_v1_backend_news_agency_server_update_item',
        };

        /**
         * @function clean
         * @memberOf NewsAgencyServerListctrl
         *
         * @description
         *   Cleans local files for a server.
         *
         * @param {Integer} index Index of the server in the array of contents.
         * @param {Integer} id    The server id.
         */
        $scope.clean = function(item) {
          var route = {
            name: 'backend_ws_news_agency_server_clean',
            params: { id: item.id }
          };

          http.post(route).then(function(response) {
            $scope.disableFlags('http');

            if (response.data.messages) {
              messenger.post(response.data.messages);
            }
          }, function() {
            $scope.disableFlags('http');
          });
        };

        /**
         * @function init
         * @memberOf CategoryListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.app.columns.selected = _.uniq($scope.app.columns.selected
            .concat([ 'name', 'synchronization', 'color', 'automatic', 'enabled' ]));

          $scope.list();
        };

        /**
         * @function emptyItem
         * @memberOf NewsAgencyServerListCtrl
         *
         * @description
         *   Removes all files downloaded from the current the sever.
         *
         * @param {Integer} id The server id.
         */
        $scope.emptyItem = function(id) {
          var route = {
            name: $scope.routes.emptyItem,
            params: { id: id }
          };

          http.put(route).then(function(response) {
            $scope.disableFlags('http');
            messenger.post(response.data);
          }, function(response) {
            $scope.disableFlags('http');
            messenger.post(response.data);
          });
        };

        /**
         * @function synchronizeItem
         * @memberOf NewsAgencyServerListCtrl
         *
         * @description
         *   Removes all files downloaded from the current the sever.
         *
         * @param {Integer} id The server id.
         */
        $scope.synchronizeItem = function(id) {
          var route = {
            name: $scope.routes.synchronizeItem,
            params: { id: id }
          };

          http.put(route).then(function(response) {
            $scope.disableFlags('http');
            messenger.post(response.data);
          }, function(response) {
            $scope.disableFlags('http');
            messenger.post(response.data);
          });
        };
      }
    ]);
})();
