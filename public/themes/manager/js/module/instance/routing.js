(function () {
  'use strict';

  angular.module('ManagerApp')
    .config(function($routeProvider, routingProvider) {
      $routeProvider
        .when(routingProvider.ngGenerateShort('manager_instances_list'), {
          templateUrl: '/managerws/template/instances:list.' + appVersion + '.tpl',
          controller: 'InstanceListCtrl',
          resolve: {
            data: function($routeParams, itemService) {
              // Default filters
              var data = {
                orderBy: [{
                  name: 'last_login',
                  value: 'desc'
                }],
                epp: 25
              };

              return itemService.list('manager_ws_instances_list', data).then(
                function(response) {
                  return response.data;
                }
              );
            }
          },
          reloadOnSearch: false
        })
        .when(routingProvider.ngGenerateShort('manager_instance_create'), {
          templateUrl: '/managerws/template/instances:item.' + appVersion + '.tpl',
          controller: 'InstanceCtrl',
          resolve: {
            data: function(itemService) {
              return itemService.new('manager_ws_instance_new').then(
                function(response) {
                  return response.data;
                }
              );
            }
          }
        })
        .when(routingProvider.ngGenerateShort('manager_instance_show', {
          id: '\:id'
        }), {
          templateUrl: '/managerws/template/instances:item.' + appVersion + '.tpl',
          controller: 'InstanceCtrl',
          resolve: {
            data: function($route, itemService) {
              return itemService.show('manager_ws_instance_show', $route.current.params.id).then(
                function(response) {
                  return response.data;
                }
              );
            }
          }
        });
    });
})();
