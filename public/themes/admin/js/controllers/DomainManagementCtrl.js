(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  DomainManagementCtrl
     *
     * @requires $controller
     * @requires $http
     * @requires $rootScope
     * @requires $scope
     * @requires routing
     * @requires messenger
     *
     * @description
     *   Controller to handle actions in domains.
     */
    .controller('DomainManagementCtrl', [
      '$controller', '$http', '$rootScope', '$scope', '$window', 'http', 'messenger', 'routing', 'webStorage',
      function($controller, $http, $rootScope, $scope, $window, http, messenger, routing, webStorage) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('CheckoutCtrl',
            { $rootScope: $rootScope, $scope: $scope }));

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
         *   Array of expanded cart.
         *
         * @type {Array}
         */
        $scope.expanded = {};

        /**
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   The name for steps.
         *
         * @type {Array}
         */
        $scope.steps = [ 'cart', 'billing', 'payment', 'summary', 'done' ];

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
         * @function cancelCreditCard
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   Cancels credit card payment.
         */
        $scope.cancelCreditCard = function() {
          $scope.nonce    = null;
          $scope.payment  = null;
          $scope.total   -= $scope.fee;
          $scope.fee      = 0;
        };

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
            domains:  $scope.cart.map(function(e) { return e.description }),
            method:   $scope.payment.type,
            nonce:    $scope.payment.nonce,
            purchase: $scope.purchase,
          };

          $http.post(url, data).then(function() {
            $scope.next();
            $scope.loading = false;
            $scope.cart = [];
          }, function(response) {
            $scope.loading = false;
            messenger.post(response.data);
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
         * @function getData
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   Returns the data to send basing on the current purchase status.
         *
         * @return {Object} The data to send.
         */
        $scope.getData = function() {
          var ids = {};
          for (var i = 0; i < $scope.cart.length; i++) {
            ids[$scope.cart[i].uuid] = $scope.cart[i].customize ? 1 : 0;
          }

          var domains = $scope.cart.map(function(e) {
            return e.description;
          });

          return {
            ids:     ids,
            domains: domains,
            method:  $scope.payment.type,
            step:    $scope.steps[$scope.step],
          };
        };

        /**
         * @function getPrice
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   Returns the price the domain price.
         *
         * @return {Float} The domain price.
         */
        $scope.getPrice = function() {
          for (var i = 0; i < $scope.extension.metas['price'].length; i++) {
            if ($scope.extension.metas['price'][i].type === 'yearly') {
              return $scope.extension.metas['price'][i].value;
            }
          }
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

          $('.uib-typeahead + .dropdown-menu')
            .width($('.uib-typeahead').width() - 6);

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

          var domains = $scope.cart.filter(function(e) {
            return e.description === domain;
          });

          if (domains.length > 0) {
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
         * @function selectedCreditCard
         * @memberOf DomainManagementCtrl
         *
         * @description
         *   Selects credit card payment.
         */
        $scope.selectCreditCard = function() {
          $scope.payment = 'card';
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
          console.log($scope.extension.uuid)
          var create = $scope.extension.uuid === 'es.openhost.domain.create'
            ? 1 : 0;

          var url = routing.generate('backend_ws_domain_check_available',
              { domain: domain, create: create });

          if (!create) {
            var url = routing.generate('backend_ws_domain_check_valid',
                { domain: domain, create: create });
          }

          $scope.loading = true;
          $http.get(url).then(function() {
            $scope.cart.push({
              id:          $scope.extension.id,
              uuid:        $scope.extension.uuid,
              name:        $scope.extension.name + ': ' + domain,
              description: domain,
              price:       $scope.extension.metas.price
            });

            $scope.domain  = '';
            $scope.loading = false;
          }, function(response) {
            $scope.loading = false;
            messenger.post({ message: response.data, type: 'error' });
          });
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

        // Update free when payment changes
        $scope.$watch('payment', function(nv) {
          $scope.fee = 0;

          if (nv && nv.type === 'CreditCard') {
            $scope.fee = ($scope.subtotal + $scope.tax) * 0.029 + 0.30;
          }
        }, true);

        // Configure braintree
        $scope.$watch('clientToken', function(nv) {
          if (!nv) {
            return;
          }

          if ($scope.clientToken && typeof braintree !== 'undefined') {
            $window.braintree.setup($scope.clientToken, 'dropin', {
              container: 'braintree-container',
              paypal: {
                container: 'braintree-container'
              },
              onError: function() {
                $scope.$apply(function() {
                  $scope.payment = null;
                  $scope.paymentLoading = false;
                });
              },
              onPaymentMethodReceived: function(obj) {
                $scope.$apply(function() {
                  $scope.payment        = obj;
                  $scope.paymentLoading = false;

                  $scope.next();
                });

                return false;
              }
            });

            $('#braintree-form').submit(function(e) {
              e.preventDefault();

              $scope.$apply(function() {
                $scope.paymentLoading = true;
              });

              return false;
            });
          }
        });

        if (webStorage.local.has('purchase')) {
          $scope.purchase = webStorage.local.get('purchase');
        }

        if (!$scope.purchase) {
          http.post('backend_ws_purchase_save').then(function(response) {
            $scope.purchase = response.data;
            webStorage.local.set('purchase', $scope.purchase);
          });
        }
    }]);
})();
