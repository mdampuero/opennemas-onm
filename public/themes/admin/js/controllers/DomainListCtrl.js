(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  DomainListCtrl
     *
     * @requires $controller
     * @requires $rootScope
     * @requires $scope
     * @requires http
     * @requires messenger
     * @requires routing
     * @requires webStorage
     *
     * @description
     *   Controller to handle actions in domains.
     */
    .controller('DomainListCtrl', [
      '$controller', '$rootScope', '$scope', 'http', 'messenger', 'routing', 'webStorage',
      function($controller, $rootScope, $scope, http, messenger, routing, webStorage) {
        /**
         * @memberOf DomainListCtrl
         *
         * @description
         *   Array of expanded cart.
         *
         * @type {Array}
         */
        $scope.expanded = {};

        /**
         * @function expand
         * @memberOf DomainListCtrl
         *
         * @description
         *   Shows/hides the information for a domain.
         */
        $scope.expand = function(index) {
          $scope.expanded[index] = !$scope.expanded[index];
        };

        /**
         * @function list
         * @memberOf DomainListCtrl
         *
         * @description
         *   Requests the list of domains.
         */
        $scope.list = function() {
          $scope.loading = true;
          http.get('backend_ws_domains_list').then(function(response) {
            $scope.loading = false;

            $scope.domains = response.data.domains;
            $scope.primary = response.data.primary;
            $scope.base    = response.data.base;
          });
        };

        /**
         * @function isRight
         * @memberOf DomainCheckoutCtrl
         *
         * @description
         *   Checks if the domain is valid.
         *
         * @return {Boolean} True if the domain is valid. Otherwise, returns
         *                   false.
         */
        $scope.isRight = function(domain) {
          return domain.target ===
            domain.name.replace('www.', '') + '.opennemas.net';
        };
    }]);
})();
