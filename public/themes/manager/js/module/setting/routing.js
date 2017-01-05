(function () {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_settings_list'), {
            templateUrl: '/managerws/template/module:list.' + appVersion + '.tpl',
            controller: 'ModuleListCtrl',
            reloadOnSearch: false
          });
      }
    ]);
})();
