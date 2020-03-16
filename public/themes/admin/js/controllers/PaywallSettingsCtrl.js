(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  PaywallSettingsCtrl
     *
     * @requires $controller
     * @requires $rootScope
     * @requires $scope
     *
     * @description
     *   Handles actions for paywall settings configuration form.
     */
    .controller('PaywallSettingsCtrl', [
      '$controller', '$http', '$rootScope', '$scope', 'messenger', 'routing',
      function($controller, $http, $rootScope, $scope, messenger, routing) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @function addPaymentMode
         * @memberOf PaywallSettingsCtrl
         *
         * @description
         *   Add a new payment mode.
         */
        $scope.addPaymentMode = function() {
          if (!$scope.settings.payment_modes) {
            $scope.settings.payment_modes = [];
          }

          $scope.settings.payment_modes.push({
            time: 'Day',
            description: '',
            price: 0
          });
        };

        /**
         * @function parseSettings
         * @memberOf PaywallSettingsCtrl
         *
         * @description
         *   Parses the current configuration.
         *
         * @param {Object} settings The current configuration.
         */
        $scope.parseSettings = function(settings) {
          if (!settings) {
            return;
          }

          settings.vat_percentage = parseInt(settings.vat_percentage);
          for (var i = 0; i < settings.payment_modes.length; i++) {
            settings.payment_modes[i].price = parseInt(settings.payment_modes[i].price);
          }

          $scope.settings = settings;
        };

        /**
         * @function getIdentification
         * @memberOf PaywallSettingsCtrl
         *
         * @description
         *   Opens a pop-up window to get Paypal identification data.
         */
        $scope.getIdentification = function() {
          var url   = 'https://www.paypal.com/us/cgi-bin/webscr?cmd=_get-api-signature&generic-flow=true';
          var title = 'PayPal identification informations';

          window.open(url, title, 'height=500, width=360, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no');
        };

        /**
         * @function removePaymentMode
         * @memberOf PaywallSettingsCtrl
         *
         * @description
         *   Removes a payment mode.
         *
         * @param {Integer} index The index of the mode to remove.
         */
        $scope.removePaymentMode = function(index) {
          $scope.settings.payment_modes.splice(index, 1);
        };

        /**
         * @function validateCredentials
         * @memberOf PaywallSettingsCtrl
         *
         * @description
         *   Validates the Paypal credentials.
         */
        $scope.validateCredentials = function() {
          $scope.validatingCredentials = true;

          var url = routing.generate('admin_paywall_validate_api');
          var data = {
            username:  $scope.settings.paypal_username,
            password:  $scope.settings.paypal_password,
            signature: $scope.settings.paypal_signature,
            mode:      $scope.settings.mode
          };

          $http.post(url, data).then(function() {
            messenger.post({
              message: 'Paypal API authentication is correct.',
              type: 'success'
            });

            $scope.validatingCredentials = false;
          }, function() {
            messenger.post({
              message: 'Paypal API authentication is incorrect. Please try again.',
              type: 'error'
            });

            $scope.validatingCredentials = false;
          });
        };

        /**
         * @function validateIpn
         * @memberOf PaywallSettingsCtrl
         *
         * @description
         *   Validates the Paypal IPN.
         */
        $scope.validateIpn = function() {
          $scope.validatingIpn = true;

          var url = routing.generate('admin_paywall_set_validate_ipn');
          var data = {
            username:  $scope.settings.paypal_username,
            password:  $scope.settings.paypal_password,
            signature: $scope.settings.paypal_signature,
            mode:      $scope.settings.mode
          };

          $http.post(url, data).then(function() {
            window.location.href = data;

            $scope.validatingIpn = false;
          }, function() {
            messenger.post({
              message: 'Could not connect to PayPal. Validate your API credentials and try again.',
              type: 'error'
            });

            $scope.validatingIpn = false;
          });
        };

        // Updates the settings to submit in form when settings object changes
        $scope.$watch('settings', function() {
          $scope.fsettings = angular.toJson($scope.settings);
        }, true);
      }
    ]);
})();
