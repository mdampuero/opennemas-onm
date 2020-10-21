(function () {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_command_output'), {
            templateUrl: '/managerws/template/framework:commands:output.' + appVersion + '.tpl',
            controller: 'CommandCtrl',
          })
          .when(routingProvider.ngGenerateShort('manager_commands'), {
            templateUrl: '/managerws/template/framework:commands:commands.' + appVersion + '.tpl',
            controller: 'CommandListCtrl',
          });
      }
    ]);
})();
