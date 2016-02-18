(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  DomainManagementCtrl
     *
     * @requires $http
     * @requires $location
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
      '$http', '$location', '$modal', '$scope', '$timeout', 'messenger', 'routing',
      function($http, $location, $modal, $scope, $timeout, messenger, routing) {
        /**
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   Flag to know if it is a purchase or redirection.
         *
         * @type {Boolean}
         */
        $scope.create = 0;

        /**
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   Array of domains.
         *
         * @type {Array}
         */
        $scope.domains = [];

        /**
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   Array of expanded domains.
         *
         * @type {Array}
         */
        $scope.expanded = {};

        /**
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   Flag to edit client information.
         *
         * @type {Boolean}
         */
        $scope.edit = false;

        /**
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   The price per module.
         *
         * @type {Integer}
         */
        $scope.price = 12;

        /**
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   The current step in the checkout wizard.
         *
         * @type {Boolean}
         */
        $scope.step = 1;

        /**
         * @memberOf StoreCheckoutCtrl
         *
         * @description
         *   Flag to know if current phone is valid.
         *
         * @type {Boolean}
         */
        $scope.validPhone = true;

        /**
         * @memberOf StoreCheckoutCtrl
         *
         * @description
         *   Flag to know if current VAT is valid.
         *
         * @type {Boolean}
         */
        $scope.validVat = true;

        /**
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   The VAT tax to apply.
         *
         * @type {Boolean}
         */
        $scope.vatTax = 0;

        /**
         * @function confirm
         * @memberOf DomainManagementCtrl
         *
         * @description
         *  Requests the purchase and shows a confirmation message.
         */
        $scope.confirm = function() {
          $scope.saving = true;
          var url = routing.generate('backend_ws_domain_save');
          var data = {
            client: $scope.client,
            create:  $scope.create,
            domains: $scope.domains,
          };

          $http.post(url, data).success(function() {
            $scope.step = 4;
            $scope.domains = [];
          });
        };

        /**
         * @function expand
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   Shows/hides the information for a domain.
         */
        $scope.expand = function(index) {
          $scope.expanded[index] = !$scope.expanded[index];
        };

        /**
         * @function isRight
         * @memberOf DomainManagementCtrl
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

          return /^(\w+\.)+\w+$/.test($scope.domain);
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
         * @function removeFromList
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   Removes a domain from domain list.
         */
        $scope.removeFromList = function(index) {
          $scope.domains.splice(index, 1);
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

          if ($scope.domains.indexOf(domain) === -1) {
            if ($scope.create) {
              $scope.loading = true;
              var url = routing.generate('backend_ws_domain_check_available',
                  { domain: domain });

              $http.get(url).success(function() {
                $scope.domains.push(domain);
                $scope.domain = '';
                $scope.loading = false;
              }).error(function(response) {
                $scope.loading = false;
                messenger.post({ message: response, type: 'error' });
              });
            } else {
              $scope.domains.push(domain);
              $scope.domain = '';
            }
          }
        };


        /**
         * @function map
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   Listens for the enter key to add a domain to map
         */
        $scope.mapByKeyPress = function(event) {
          if ($scope.isValid() && event.keyCode === 13) {
            $scope.map();
          }
        };

        // Updates the edit flag when client changes.
        $scope.$watch('client', function(nv) {
          if (!nv || !nv.first_name) {
            $scope.edit = true;
            $scope.validPhone = false;
            $scope.validVat   = false;
          }
        });

        // Updates vat and total values when vat tax changes
        $scope.$watch('validVat', function(nv) {
          if (nv === true) {
            $scope.vat   = ($scope.subtotal * $scope.vatTax) / 100;
            $scope.total = $scope.subtotal + $scope.vat;
          }
        });

        // Updates the edit flag when client changes.
        $scope.$watch('[client.company, client.country, client.vat_number]', function() {
          if (!$scope.client) {
            return;
          }

          $scope.vatTax = 0;

          // Individual customer
          if (!$scope.client.company && $scope.client.country &&
              $scope.taxes[$scope.client.country]) {
            $scope.vatTax = $scope.taxes[$scope.client.country].value;
            return;
          }

          // Spanish company
          if ($scope.client.company && $scope.client.country === 'ES' &&
              $scope.taxes[$scope.client.country]) {
            $scope.vatTax = $scope.taxes[$scope.client.country].value;
          }
        }, true);

        $scope.$watch('client.country', function(nv, ov) {
          if (!nv) {
            return;
          }

          var url = routing.generate('backend_ws_store_check_phone',
              { country: $scope.client.country, phone: $scope.client.phone });

          $http.get(url).success(function() {
            $scope.validPhone = true;
          }).error(function() {
            $scope.validPhone = false;
          });

          url = routing.generate('backend_ws_store_check_vat',
              { country: $scope.client.country, vat: $scope.client.vat_number });

          $http.get(url).success(function() {
            $scope.validVat = true;
          }).error(function() {
            $scope.validVat = false;
          });
        }, true);


        // Updates the edit flag when client changes.
        $scope.$watch('client.phone', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if (!$scope.client || !$scope.client.country ||
              !$scope.client.phone) {
            $scope.validPhone = false;
            return;
          }

          if ($scope.searchTimeout) {
            $timeout.cancel($scope.searchTimeout);
          }

          var url = routing.generate('backend_ws_store_check_phone',
              { country: $scope.client.country, phone: $scope.client.phone });

          $scope.searchTimeout = $timeout(function() {
            $http.get(url).success(function() {
              $scope.validPhone = true;
            }).error(function() {
              $scope.validPhone = false;
            });
          }, 500);
        }, true);

        // Updates the edit flag when client changes.
        $scope.$watch('client.vat_number', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if (!$scope.client || !$scope.client.country ||
              !$scope.client.vat_number) {
            $scope.validVat = false;
            return;
          }

          if ($scope.searchTimeout) {
            $timeout.cancel($scope.searchTimeout);
          }

          var url = routing.generate('backend_ws_store_check_vat',
              { country: $scope.client.country, vat: $scope.client.vat_number });

          $scope.searchTimeout = $timeout(function() {
            $http.get(url).success(function() {
              $scope.validVat = true;
            }).error(function() {
              $scope.validVat = false;
            });
          }, 500);
        }, true);

        // Updates domain price when create flag changes
        $scope.$watch('create', function(nv, ov) {
          if (nv === 1) {
            $scope.price = 18;
          }
        });

        // Updates total and vat when domain change
        $scope.$watch('domains', function(nv, ov) {
          if (ov === nv) {
            return;
          }

          if (nv.length > 0) {
            $scope.subtotal = $scope.price * nv.length;
            $scope.vat      = Math.round($scope.subtotal * $scope.vatTax)/100;
            $scope.total    = $scope.subtotal + $scope.vat;
          }
        }, true);
    }]);
})();
