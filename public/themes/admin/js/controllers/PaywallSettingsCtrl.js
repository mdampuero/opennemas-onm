(function () {
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
    .controller('PaywallSettingsCtrl', ['$controller', '$rootScope', '$scope',
      function($controller, $rootScope, $scope) {
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

        // Updates the settings to submit in form when settings object changes
        $scope.$watch('settings', function() {
          $scope.fsettings = angular.toJson($scope.settings);
        }, true);
      }
    ]);
})();
