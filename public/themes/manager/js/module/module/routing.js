(function () {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_modules_list'), {
            templateUrl: '/managerws/template/module:list.' + appVersion + '.tpl',
            controller: 'ModuleListCtrl',
            reloadOnSearch: false
          })
          .when(routingProvider.ngGenerateShort('manager_module_create'), {
            templateUrl: '/managerws/template/module:item.' + appVersion + '.tpl',
            controller: 'ModuleCtrl',
          })
          .when(routingProvider.ngGenerateShort('manager_module_show', {
            id: '\:id'
          }), {
            templateUrl: '/managerws/template/module:item.' + appVersion + '.tpl',
            controller: 'ModuleCtrl',
          });
      }
    ]);
})();
