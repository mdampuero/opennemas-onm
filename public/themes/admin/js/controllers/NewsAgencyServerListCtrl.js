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
          deleteItem: 'api_v1_backend_news_agency_server_delete_item',
          deleteList: 'api_v1_backend_news_agency_server_delete_list',
          getList:    'api_v1_backend_news_agency_server_get_list',
          patchItem:  'api_v1_backend_news_agency_server_patch_item',
          patchList:  'api_v1_backend_news_agency_server_patch_list',
          redirect:   'backend_news_agency_server_show',
          updateItem: 'api_v1_backend_news_agency_server_update_item',
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

          http.post(route).success(function(response) {
            $scope.disableFlags('http');

            if (response.messages) {
              messenger.post(response.messages);
            }
          }).error(function() {
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
          $scope.list();
        };
      }
    ]);
})();
