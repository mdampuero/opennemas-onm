(function () {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_notifications_list'), {
            templateUrl: '/managerws/template/notification:list.' + appVersion + '.tpl',
            controller: 'NotificationListCtrl',
            reloadOnSearch: false
          })
          .when(routingProvider.ngGenerateShort('manager_notification_create'), {
            templateUrl: '/managerws/template/notification:item.' + appVersion + '.tpl',
            controller: 'NotificationCtrl',
          })
          .when(routingProvider.ngGenerateShort('manager_notification_show', {
            id: '\:id'
          }), {
            templateUrl: '/managerws/template/notification:item.' + appVersion + '.tpl',
            controller: 'NotificationCtrl',
          });
      }
    ]);
})();
