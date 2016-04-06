(function () {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_reports_list'), {
            templateUrl: '/managerws/template/report:list.' + appVersion + '.tpl',
            controller: 'ReportListCtrl',
            reloadOnSearch: false
          })
          .when(routingProvider.ngGenerateShort('manager_command_output'), {
            templateUrl: '/managerws/template/framework:commands:output.' + appVersion + '.tpl',
            controller: 'CommandCtrl',
          });
      }
    ]);
})();
