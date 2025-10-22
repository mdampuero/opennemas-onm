(function() {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_security_two_factor'), {
            templateUrl: '/managerws/template/security:twoFactor.' + appVersion + '.tpl',
            controller: 'SecurityTwoFactorCtrl',
            reloadOnSearch: false
          });
      }
    ]);
})();
