(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  DomainManagementCtrl
     *
     * @requires $http
     * @requires $location
     * @requires $uibModal
     * @requires $scope
     * @requires $timeout
     * @requires routing
     * @requires messenger
     *
     * @description
     *   description
     */
    .controller('DomainManagementCtrl', [
      '$http', '$location', '$rootScope', '$scope', '$timeout', '$uibModal', '$window', 'messenger', 'routing',
      function($http, $location, $rootScope, $scope, $timeout, $uibModal, $window, messenger, routing) {
        /**
         * @memberOf DomainManagementCtrl
         *
         * @description
         *  Flag for card request loading.
         *
         * @type {boolean}
         */
        $scope.cardLoading = false;

        /**
         * @memberOf DomainManagementCtrl
         *
         * @description
         *  Flag for valid client.
         *
         * @type {boolean}
         */
        $scope.clientValid = false;

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
         *  The current payment method.
         *
         * @type {String}
         */
        $scope.payment = '';

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
        $scope.width = $('.uib-typeahead').width();

        /**
         * @function confirm
         * @memberOf DomainManagementCtrl
         *
         * @description
         *  Requests the purchase and shows a confirmation message.
         */
        $scope.confirm = function() {
          $scope.loading = true;
          var url = routing.generate('backend_ws_domain_save');
          var data = {
            client:  $scope.client,
            create:  $scope.create,
            domains: $scope.domains,
            nonce:   $scope.nonce
          };

          $http.post(url, data).success(function() {
            $scope.step = 4;
            $scope.loading = false;
            $scope.domains = [];
          }).error(function(response) {
            $scope.loading = false;
            messenger.post(response);
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

          $('.uib-typeahead + .dropdown-menu').width($scope.width - 24);

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
                { domain: domain, create: $scope.create });

            if (!$scope.create) {
              var url = routing.generate('backend_ws_domain_check_valid',
                  { domain: domain, create: $scope.create });
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

        /**
         * @function next
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   Goes to the next step.
         */
        $scope.next = function() {
          $scope.step++;

          if ($scope.step === 2 && $scope.client) {
            $scope.step++;
          }
        };

        /**
         * @function toggleCardLoading
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   Toggles cardLoading flag.
         */
        $scope.toggleCardLoading = function() {
          $scope.cardLoading = !$scope.cardLoading;
        };

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
          }
        }, true);

        $scope.$watch('step', function(nv) {
          if (nv !== 3) {
            return;
          }

          if ($scope.client.country === 'ES' || (!$scope.client.company &&
                $scope.countries[$scope.client.country])) {
            $scope.vatTax = $scope.taxes[$scope.client.country].value;
          }

          $scope.vat   = Math.round($scope.subtotal * $scope.vatTax)/100;
          $scope.total = $scope.subtotal + $scope.vat;
        });

        $scope.$watch('payment', function(nv) {
          if (nv === 'card') {
            $scope.fee   = ($scope.subtotal + $scope.vat) * 0.029 + 0.30;
            $scope.total = $scope.subtotal + $scope.vat + $scope.fee;
          }
        });

        // Get client after saving
        $rootScope.$on('client-saved', function (event, args) {
          $scope.client = args;
          $scope.next();
        });

        // Configure braintree
        $scope.$watch('clientToken', function(nv) {
          if (!nv) {
            return;
          }

          if ($scope.clientToken && typeof braintree !== 'undefined') {
            $window.braintree.setup($scope.clientToken, 'custom', {
              id: 'checkout',
              hostedFields: {
                number: {
                  selector: '#card-number'
                },
                cvv: {
                  selector: '#cvv'
                },
                expirationDate: {
                  selector: '#expiration-date'
                }
              },
              onError: function (error) {
                $scope.toggleCardLoading();
                $scope.$apply(function() {
                  $scope.error = error.message;
                });
              },
              onPaymentMethodReceived: function (nonce) {
                $scope.toggleCardLoading();
                $scope.$apply(function() {
                  $scope.error   = null;
                  $scope.nonce   = nonce.nonce;
                  $scope.payment = 'card';
                });

                return false;
              },
              paypal: {
                container: 'paypal-container',
                onCancelled: function() {
                  $scope.$apply(function() {
                    $scope.nonce   = null;
                    $scope.payment = null;
                  });
                },
                onSuccess: function (nonce) {
                  $scope.$apply(function() {
                    $scope.nonce   = nonce;
                    $scope.payment = 'paypal';
                  });

                  return false;
                }
              }
            });
          }
        });
    }]);
})();
