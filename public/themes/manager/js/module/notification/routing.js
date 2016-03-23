(function () {
  'use strict';

  angular.module('ManagerApp')
    .config(function($routeProvider, routingProvider) {
      $routeProvider
        .when(routingProvider.ngGenerateShort('manager_notifications_list'), {
          templateUrl: '/managerws/template/notification:list.' + appVersion + '.tpl',
          controller: 'NotificationListCtrl',
          resolve: {
            data: function($routeParams, itemService) {
              var data = { oql: 'order by id asc limit 25' };

              return itemService.list('manager_ws_notifications_list', data).then(
                function(response) {
                  return response.data;
                }
              );
            }
          },
          reloadOnSearch: false
        })
        .when(routingProvider.ngGenerateShort('manager_notification_create'), {
          templateUrl: '/managerws/template/notification:item.' + appVersion + '.tpl',
          controller: 'NotificationCtrl',
          resolve: {
            data: function(itemService) {
              return itemService.new('manager_ws_notification_new').then(
                function(response) {
                  return response.data;
                }
              );
            }
          }
        })
        .when(routingProvider.ngGenerateShort('manager_notification_show', {
          id: '\:id'
        }), {
          templateUrl: '/managerws/template/notification:item.' + appVersion + '.tpl',
          controller: 'NotificationCtrl',
          resolve: {
            data: function($route, itemService) {
              return itemService.show('manager_ws_notification_show', $route.current.params.id).then(
                function(response) {
                  return response.data;
                }
              );
            }
          }
        })
    });
})();
