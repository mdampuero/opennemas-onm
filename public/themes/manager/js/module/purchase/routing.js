(function () {
  'use strict';
  angular.module('ManagerApp')
    .config(function($routeProvider, routingProvider) {
      $routeProvider
        .when(routingProvider.ngGenerateShort('manager_purchases_list'), {
          templateUrl: '/managerws/template/purchase:list.' + appVersion + '.tpl',
          controller: 'PurchaseListCtrl',
          resolve: {
            data: function($routeParams, itemService) {
              // Default filters
              var data = {
                orderBy: [{
                  name: 'created',
                  value: 'desc'
                }],
                epp: 25
              };

              return itemService.list('manager_ws_purchases_list', data).then(
                function(response) {
                  return response.data;
                }
              );
            }
          },
          reloadOnSearch: false
        })
        .when(routingProvider.ngGenerateShort('manager_purchase_create'), {
          templateUrl: '/managerws/template/purchase:item.' + appVersion + '.tpl',
          controller: 'PurchaseCtrl',
          resolve: {
            data: function(itemService) {
              return itemService.new('manager_ws_purchase_new').then(
                function(response) {
                  return response.data;
                }
              );
            }
          }
        })
        .when(routingProvider.ngGenerateShort('manager_purchase_show', {
          id: '\:id'
        }), {
          templateUrl: '/managerws/template/purchase:item.' + appVersion + '.tpl',
          controller: 'PurchaseCtrl',
          resolve: {
            data: function($route, itemService) {
              return itemService.show('manager_ws_purchase_show', $route.current.params.id).then(
                function(response) {
                  return response.data;
                }
              );
            }
          }
        })
    });
})();
