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
      '$controller', '$http', '$uibModal', '$scope', 'http', 'itemService', 'routing', 'messenger',
      function($controller, $http, $uibModal, $scope, http, itemService, routing, messenger) {

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

        /**
         * @function patch
         * @memberOf NewsAgencyServerListCtrl
         *
         * @description
         *   Enables/disables a server.
         *
         * @param {String}  item     The server object.
         * @param {String}  property The property name.
         * @param {Boolean} value    The property value.
         */
        $scope.patch = function(item, property, value) {
          var data = {};

          item[property + 'Loading'] = 1;
          data[property] = value;

          var route = {
            name:   'backend_ws_news_agency_server_toggle',
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
      }
    ]);
})();
