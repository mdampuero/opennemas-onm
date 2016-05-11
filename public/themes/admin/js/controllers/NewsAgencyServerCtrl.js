(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  NewsAgencyServerCtrll
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
    .controller('NewsAgencyServerCtrl', [
      '$http', '$scope', 'routing', 'messenger',
      function($http, $scope, routing, messenger) {
        /**
         * @memberOf NewsAgencyServerCtrl
         *
         * @description
         *  Connection checked flag
         *
         * @type {Boolean}
         */
        $scope.test = false;

        /**
         * @function check
         * @memberOf NewsAgencyServerCtrl
         *
         * @description
         *   Checks the connection to the server.
         */
        $scope.check = function () {
          $scope.checking = true;

          var url = routing.generate('backend_ws_news_agency_server_check', {
            password: $scope.password,
            url:      $scope.url,
            username: $scope.username
          });

          $http.get(url).then(function(response) {
            $scope.checking = false;
            $scope.test = true;
            messenger.post(response.data);
          }, function(response) {
            $scope.checking = false;
            messenger.post(response.data);
          });
        };
      }
    ]);
})();
