(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  DomainManagementCtrl
     *
     * @requires $http
     * @requires $modal
     * @requires $scope
     * @requires $timeout
     * @requires routing
     * @requires messenger
     *
     * @description
     *   description
     */
    .controller('DomainManagementCtrl', [
      '$http', '$modal', '$scope', '$timeout', 'routing', 'messenger',
      function($http, $modal, $scope, $timeout, routing, messenger) {
        $scope.domains = [];

        $scope.expanded = {};

        $scope.expand = function(index) {
          $scope.expanded[index] = !$scope.expanded[index];

          if ($scope.domains[index].target) {
            return;
          }

          $scope.domains[index].loading = true;

          if ($scope.expanded[index]) {
            var url = routing.generate('backend_ws_domain_show',
                { id: $scope.domains[index].name });

            $http.get(url).then(function(response) {
              $scope.domains[index].loading = false;
              $scope.domains[index].target  = response.data.target;
              $scope.domains[index].expires = response.data.expires;
            });
          }
        };

        /**
         * @function isValid
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   Checks if the domain is valid.
         *
         * @return {Boolean} True if the domain is valid. Otherwise, returns
         *                   false.
         */
        $scope.isValid = function() {
          if ($scope.domains.indexOf('www.' + $scope.domain) !== -1) {
            return false;
          }

          return /(\w+\.)+\w+/.test($scope.domain);
        };

        /**
         * @function list
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   Requests the list of domains.
         */
        $scope.list = function() {
          $scope.loading = true;
          var url = routing.generate('backend_ws_domains_list');
          $http.get(url).then(function(response) {
            $scope.loading = false;

            $scope.domains = response.data.domains;
            $scope.primary = response.data.primary;
            $scope.base    = response.data.base;
          });
        };

        /**
         * @function map
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   Maps the domain.
         */
        $scope.map = function() {
          var domain = 'www.' + $scope.domain;
          var url = routing.generate('backend_ws_domain_check', { domain: domain });

          $scope.loading = true;
          $http.get(url).then(function(response) {
          $scope.loading = false;
            if (response.status === 400) {
              messenger.post({ message: response.data, type: 'error' });
            }

            if (response.status === 200) {
              if ($scope.domains.indexOf(domain) === -1) {
                $scope.domains.push(domain);
              }

              $scope.domain = null;
            }
          });
        };

        // Updates total and vat when domain change
        $scope.$watch('domains', function(nv, ov) {
          if (ov === nv) {
            return;
          }

          if (nv.length > 0) {
            $scope.subtotal = 12 * nv.length;
            $scope.vat      = $scope.subtotal * 0.21;
            $scope.total    = $scope.subtotal + $scope.vat;
          }
        }, true);
    }]);
})();
