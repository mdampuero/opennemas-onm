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
         *   The domain to add.
         *
         * @type {Object}
         */
        $scope.domain = '';

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
         *   Flag to edit billing information.
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
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   The suggestions list.
         *
         * @type {Array}
         */
        $scope.suggests = [];

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
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   The width of the suggetion list container.
         *
         * @type {Integer}
         */
        $scope.width = $('.typeahead').width();

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
            billing: $scope.billing,
            create:  $scope.create,
            domains: $scope.domains
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
         * @function expand
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   Creates a suggestion list basing on a domain without TLD.
         *
         * @param {String} domain The input domain.
         */
        $scope.getSuggestions = function(domain) {
          var name = domain;
          var tld  = '';

          if (domain.lastIndexOf('.') !== -1) {
            name = domain.substring(0, domain.lastIndexOf('.'));
            tld  = domain.substring(domain.lastIndexOf('.'));
          }

          var tlds = [
            '.com', '.net', '.co.uk', '.es', '.cat', '.ch', '.cz', '.de',
            '.dk', '.at', '.be', '.eu', '.fi', '.fr', '.in', '.info', '.it',
            '.li', '.lt', '.mobi', '.name', '.nl', '.nu', '.org', '.pl',
            '.pro', '.pt', '.re', '.se', '.tel', '.tf', '.us', '.wf', '.yt',
          ];

          var suggestions = [];

          for (var i = 0; i < tlds.length; i++) {
            if (!tld || tlds[i] !== tld) {
              suggestions.push(name + tlds[i]);
            }
          }

          $('.suggestions').width($scope.width - 24);

          return suggestions;
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
          var domain = 'www.' + $scope.domain;

          if ($scope.domains.indexOf(domain) !== -1) {
            return false;
          }

          return /^(\w+)+(\.\w+)*$/.test(domain);
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
          if (!$scope.domain) {
            return;
          }

          var domain = 'www.' + $scope.domain;

          if ($scope.domains.indexOf(domain) === -1) {
            var url = routing.generate('backend_ws_domain_check_available',
                { domain: domain });

            if (!$scope.create) {
              var url = routing.generate('backend_ws_domain_check_valid',
                  { domain: domain });
            }

            $scope.loading = true;
            $http.get(url).success(function() {
              $scope.domains.push(domain);
              $scope.domain = '';
              $scope.loading = false;
            }).error(function(response) {
              $scope.loading = false;
              messenger.post({ message: response, type: 'error' });
            });
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

        // Updates the edit flag when billing changes.
        $scope.$watch('billing', function(nv) {
          if (!nv || !nv.name) {
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

        // Updates the edit flag when billing changes.
        $scope.$watch('[billing.company, billing.country, billing.vat]', function() {
          if (!$scope.billing) {
            return;
          }

          $scope.vatTax = 0;

          // Individual customer
          if (!$scope.billing.company && $scope.billing.country &&
              $scope.taxes[$scope.billing.country]) {
            $scope.vatTax = $scope.taxes[$scope.billing.country].value;
            return;
          }

          // Spanish company
          if ($scope.billing.company && $scope.billing.country === 'ES' &&
              $scope.taxes[$scope.billing.country]) {
            $scope.vatTax = $scope.taxes[$scope.billing.country].value;
          }
        }, true);

        $scope.$watch('billing.country', function(nv) {
          if (!nv) {
            return;
          }

          var url = routing.generate('backend_ws_store_check_phone',
              { country: $scope.billing.country, phone: $scope.billing.phone });

          $http.get(url).success(function() {
            $scope.validPhone = true;
          }).error(function() {
            $scope.validPhone = false;
          });

          url = routing.generate('backend_ws_store_check_vat',
              { country: $scope.billing.country, vat: $scope.billing.vat });

          $http.get(url).success(function() {
            $scope.validVat = true;
          }).error(function() {
            $scope.validVat = false;
          });
        }, true);

        // Updates the edit flag when billing changes.
        $scope.$watch('billing.phone', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if (!$scope.billing || !$scope.billing.country ||
              !$scope.billing.phone) {
            $scope.validPhone = false;
            return;
          }

          if ($scope.searchTimeout) {
            $timeout.cancel($scope.searchTimeout);
          }

          var url = routing.generate('backend_ws_store_check_phone',
              { country: $scope.billing.country, phone: $scope.billing.phone });

          $scope.searchTimeout = $timeout(function() {
            $http.get(url).success(function() {
              $scope.validPhone = true;
            }).error(function() {
              $scope.validPhone = false;
            });
          }, 500);
        }, true);

        // Updates the edit flag when billing changes.
        $scope.$watch('billing.vat', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if (!$scope.billing || !$scope.billing.country ||
              !$scope.billing.vat) {
            $scope.validVat = false;
            return;
          }

          if ($scope.searchTimeout) {
            $timeout.cancel($scope.searchTimeout);
          }

          var url = routing.generate('backend_ws_store_check_vat',
              { country: $scope.billing.country, vat: $scope.billing.vat });

          $scope.searchTimeout = $timeout(function() {
            $http.get(url).success(function() {
              $scope.validVat = true;
            }).error(function() {
              $scope.validVat = false;
            });
          }, 500);
        }, true);

        // Updates domain price when create flag changes
        $scope.$watch('create', function(nv) {
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
