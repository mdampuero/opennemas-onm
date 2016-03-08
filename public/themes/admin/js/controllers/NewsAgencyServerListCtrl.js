(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  NewsAgencyServerListCtrl
     *
     * @requires $controller
     * @requires $http
     * @requires $uibModal
     * @requires $scope
     * @requires itemService
     * @requires routing
     * @requires messenger
     *
     * @description
     *   Controller for server list in news agency.
     */
    .controller('NewsAgencyServerListCtrl', [
      '$controller', '$http', '$uibModal', '$scope', 'itemService', 'routing', 'messenger',
      function($controller, $http, $uibModal, $scope, itemService, routing, messenger) {

        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentListCtrl', {$scope: $scope}));

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
        $scope.clean = function (index, id) {
          $scope.contents[index].cleaning = true;

          var url = routing.generate('backend_ws_news_agency_server_clean',
              { id: id });

          $http.post(url).success(function(response) {
            $scope.contents[index].cleaning = false;

            if (response.messages) {
              messenger.post(response.messages);
            }
          }).error(function() {
            $scope.contents[index].cleaning = false;
          });
        };
      }
    ]);
})();
