(function () {
  'use strict';
  angular.module('ManagerApp')
    .config(function($routeProvider, routingProvider) {
      $routeProvider
        .when(routingProvider.ngGenerateShort('manager_clients_list'), {
          templateUrl: '/managerws/template/client:list.' + appVersion + '.tpl',
          controller: 'ClientListCtrl',
          resolve: {
            data: function($routeParams, itemService) {
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
        });
    });
})();
