(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  DomainCheckoutCtrl
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
    .controller('DomainCheckoutCtrl', [
      '$controller', '$rootScope', '$scope', 'http', 'messenger', 'routing', 'webStorage',
      function($controller, $rootScope, $scope, http, messenger, routing, webStorage) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('CheckoutCtrl',
            { $rootScope: $rootScope, $scope: $scope }));

        /**
         * @memberOf StoreCheckoutCtrl
         *
         * @description
         *  The shopping cart name.
         *
         * @type {String}
         */
        $scope.cartName = 'cart_domain_redirect';

        /**
         * @memberOf DomainCheckoutCtrl
         *
         * @description
         *   The domain to add.
         *
         * @type {Object}
         */
        $scope.domain = '';

        /**
         * @memberOf DomainCheckoutCtrl
         *
         * @description
         *   Array of expanded cart.
         *
         * @type {Array}
         */
        $scope.expanded = {};

        /**
         * @memberOf DomainCheckoutCtrl
         *
         * @description
         *   The name for steps.
         *
         * @type {Array}
         */
        $scope.steps = [ 'cart', 'billing', 'payment', 'summary', 'done' ];

        /**
         * @memberOf DomainCheckoutCtrl
         *
         * @description
         *   The suggestions list.
         *
         * @type {Array}
         */
        $scope.suggests = [];

        /**
         * @function confirm
         * @memberOf DomainCheckoutCtrl
         *
         * @description
         *  Requests the purchase and shows a confirmation message.
         */
        $scope.confirm = function() {
          $scope.loading = true;

          var data = { purchase: $scope.purchase };

          if ($scope.payment.type) {
            data.method = $scope.payment.type;
            data.nonce  = $scope.payment.nonce;
          }

          http.post('backend_ws_domain_save', data).then(function() {
            $scope.next();
            $scope.loading = false;
            $scope.cart = [];
          }, function() {
            $scope.error   = true;
            $scope.loading = false;
            webStorage.local.remove('purchase');
          });
        };

        /**
         * @function expand
         * @memberOf DomainCheckoutCtrl
         *
         * @description
         *   Shows/hides the information for a domain.
         */
        $scope.expand = function(index) {
          $scope.expanded[index] = !$scope.expanded[index];
        };

        /**
         * @function getCart
         * @memberOf DomainCheckoutCtrl
         *
         * @description
         *   Gets the cart from local storage.
         */
        $scope.getCart = function() {
          // Initialize the shopping cart from the webStorage
          if (webStorage.local.has($scope.cartName)) {
            $scope.cart = webStorage.local.get($scope.cartName);
          }
        };

        /**
         * @function getData
         * @memberOf DomainCheckoutCtrl
         *
         * @description
         *   Returns the data to send basing on the current purchase status.
         *
         * @return {Object} The data to send.
         */
        $scope.getData = function() {
          var ids = {};
          for (var i = 0; i < $scope.cart.length; i++) {
            ids[$scope.cart[i].uuid] = 'yearly';
          }

          var domains = $scope.cart.map(function(e) {
            return e.description;
          });

          var data = {
            ids:    ids,
            params: {},
            step:   $scope.steps[$scope.step + 1]
          };

          data.params[$scope.extension.uuid] = domains;

          if ($scope.payment.type) {
            data.method = $scope.payment.type;
          }

          return data;
        };

        /**
         * @function expand
         * @memberOf DomainCheckoutCtrl
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
         * @function init
         * @memberOf DomainCheckoutCtrl
         *
         * @description
         *   Starts the checkout process.
         */
        $scope.init = function () {
          if (!$scope.purchase) {
            var data = $scope.getData();

            http.post('backend_ws_purchase_save', data).then(function(response) {
            $scope.purchase = response.data.id;
            webStorage.local.set('purchase', $scope.purchase);
            });
          } else {
            $scope.start();
          }
        };

        /**
         * @function isValid
         * @memberOf DomainCheckoutCtrl
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
         * @memberOf DomainCheckoutCtrl
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
         * @function selectedCreditCard
         * @memberOf DomainCheckoutCtrl
         *
         * @description
         *   Selects credit card payment.
         */
        $scope.selectCreditCard = function() {
          $scope.payment = 'card';
        };

        /**
         * @function map
         * @memberOf DomainCheckoutCtrl
         *
         * @description
         *   Maps the domain.
         */
        $scope.map = function() {
          if (!$scope.domain) {
            return;
          }

          var domain = 'www.' + $scope.domain;
          var create = $scope.extension.uuid === 'es.openhost.domain.create' ?
            1 : 0;

          var route = {
            name: 'backend_ws_domain_check_available',
            params: { create: create, domain: domain }
          };

          if (!create) {
            route.name = 'backend_ws_domain_check_valid';
          }

          $scope.loading = true;
          http.get(route).then(function() {
            $scope.cart.push({
              id:          $scope.extension.id,
              uuid:        $scope.extension.uuid,
              name:        $scope.extension.name + ': ' + domain,
              description: domain,
              price:       $scope.extension.price
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
         * @memberOf DomainCheckoutCtrl
         *
         * @description
         *   Listens for the enter key to add a domain to map
         */
        $scope.mapByKeyPress = function(event) {
          if ($scope.isValid() && event.keyCode === 13) {
            $scope.map();
          }
        };

        // Updates cart name when extension changes
        $scope.$watch('extension', function(nv) {
          $scope.cartName = 'cart_domain_redirect';

          if (nv && nv.uuid && nv.uuid === 'es.openhost.domain.create') {
            $scope.cartName = 'cart_domain_create';
          }

          $scope.getCart();
        }, true);
    }]);
})();
