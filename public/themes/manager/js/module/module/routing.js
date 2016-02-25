(function () {
  'use strict';

  angular.module('ManagerApp')
    .config(function($routeProvider, routingProvider) {
      $routeProvider
        .when(routingProvider.ngGenerateShort('manager_modules_list'), {
          templateUrl: '/managerws/template/module:list.' + appVersion + '.tpl',
          controller: 'ModuleListCtrl',
          resolve: {
            data: function($routeParams, itemService) {
              // Default filters
              var data = {
                epp: 25,
                orderBy: [{
                  name: 'uuid',
                  value: 'asc'
                }],
              };

              return itemService.list('manager_ws_modules_list', data).then(
                function(response) {
                  return response.data;
                }
              );
            }
          },
          reloadOnSearch: false
        })
        .when(routingProvider.ngGenerateShort('manager_module_create'), {
          templateUrl: '/managerws/template/module:item.' + appVersion + '.tpl',
          controller: 'ModuleCtrl',
          resolve: {
            data: function(itemService) {
              return itemService.new('manager_ws_module_new').then(
                function(response) {
                  return response.data;
                }
              );
            }
          }
        })
        .when(routingProvider.ngGenerateShort('manager_module_show', {
          id: '\:id'
        }), {
          templateUrl: '/managerws/template/module:item.' + appVersion + '.tpl',
          controller: 'ModuleCtrl',
          resolve: {
            data: function($route, itemService) {
              return itemService.show('manager_ws_module_show', $route.current.params.id).then(
                function(response) {
                  return response.data;
                }
              );
            }
          }
        });
    });
})();
