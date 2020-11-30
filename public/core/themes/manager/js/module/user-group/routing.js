(function () {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_user_groups_list'), {
            templateUrl: '/managerws/template/user_group:list.' + appVersion + '.tpl',
            controller: 'UserGroupListCtrl',
            reloadOnSearch: false
          })
          .when(routingProvider.ngGenerateShort('manager_user_group_create'), {
            templateUrl: '/managerws/template/user_group:item.' + appVersion + '.tpl',
            controller: 'UserGroupCtrl',
          })
          .when(routingProvider.ngGenerateShort('manager_user_group_show', {
            id: '\:id'
          }), {
            templateUrl: '/managerws/template/user_group:item.' + appVersion + '.tpl',
            controller: 'UserGroupCtrl',
          });
      }
    ]);
})();
