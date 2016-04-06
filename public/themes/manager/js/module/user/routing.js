(function () {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_users_list'), {
            templateUrl: '/managerws/template/user:list.' + appVersion + '.tpl',
            controller: 'UserListCtrl',
            reloadOnSearch: false
          })
          .when(routingProvider.ngGenerateShort('manager_user_create'), {
            templateUrl: '/managerws/template/user:item.' + appVersion + '.tpl',
            controller: 'UserCtrl',
          })
          .when(routingProvider.ngGenerateShort('manager_user_show', {
            id: '\:id'
          }), {
            templateUrl: '/managerws/template/user:item.' + appVersion + '.tpl',
            controller: 'UserCtrl',
          });
      }
    ]);
})();
