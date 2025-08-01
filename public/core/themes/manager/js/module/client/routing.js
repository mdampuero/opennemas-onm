(function () {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_clients_list'), {
            templateUrl: '/managerws/template/client:list.' + appVersion + '.tpl',
            controller: 'ClientListCtrl',
            reloadOnSearch: false
          })
        .when(routingProvider.ngGenerateShort('manager_client_create'), {
          templateUrl: '/managerws/template/client:item.' + appVersion + '.tpl',
          controller: 'ClientCtrl',
          reloadOnSearch: false
        })
        .when(routingProvider.ngGenerateShort('manager_client_show', { id: '\:id' }), {
          templateUrl: '/managerws/template/client:item.' + appVersion + '.tpl',
          controller: 'ClientCtrl',
          reloadOnSearch: false
        });
      }
    ]);
})();
