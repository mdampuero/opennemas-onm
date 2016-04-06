(function () {
  'use strict';
  angular.module('ManagerApp')
    .config(function($routeProvider, routingProvider) {
      $routeProvider
        .when(routingProvider.ngGenerateShort('manager_clients_list'), {
          templateUrl: '/managerws/template/client:list.' + appVersion + '.tpl',
          controller: 'ClientListCtrl',
          resolve: {
            data: function($route, itemService) {
              // Default filters
              var data = {
                orderBy: [{
                  name: 'id',
                  value: 'asc'
                }],
                epp: 25
              };

              return itemService.list('manager_ws_clients_list', data).then(
                function(response) {
                  return response.data;
                }
              );
            }
          },
          reloadOnSearch: false
        })
        .when(routingProvider.ngGenerateShort('manager_client_create'), {
          templateUrl: '/managerws/template/client:item.' + appVersion + '.tpl',
          controller: 'ClientCtrl',
          resolve: {
            data: function($route, itemService) {
              return itemService.show('manager_ws_client_new').then(
                function(response) {
                  return response.data;
                }
              );
            }
          },
          reloadOnSearch: false
        })
        .when(routingProvider.ngGenerateShort('manager_client_show', { id: '\:id' }), {
          templateUrl: '/managerws/template/client:item.' + appVersion + '.tpl',
          controller: 'ClientCtrl',
          resolve: {
            data: function($route, itemService) {
              return itemService.show('manager_ws_client_show', $route.current.params.id).then(
                function(response) {
                  return response.data;
                }
              );
            }
          },
          reloadOnSearch: false
        });
    });
})();
