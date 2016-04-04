(function () {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_instances_list'), {
            templateUrl: '/managerws/template/instances:list.' + appVersion + '.tpl',
            controller: 'InstanceListCtrl',
            reloadOnSearch: false
          })
          .when(routingProvider.ngGenerateShort('manager_instance_create'), {
            templateUrl: '/managerws/template/instances:item.' + appVersion + '.tpl',
            controller: 'InstanceCtrl',
          })
          .when(routingProvider.ngGenerateShort('manager_instance_show', { id: '\:id' }), {
            templateUrl: '/managerws/template/instances:item.' + appVersion + '.tpl',
            controller: 'InstanceCtrl',
          });
      }
    ]);
})();
