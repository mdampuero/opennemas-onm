(function () {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_opcache_status'), {
            templateUrl: '/managerws/template/framework:opcache_status.' + appVersion + '.tpl',
            controller: 'OpcacheCtrl',
          });
      }
    ]);
})();
