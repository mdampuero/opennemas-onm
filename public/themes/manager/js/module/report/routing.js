(function () {
  'use strict';

  angular.module('ManagerApp')
    .config(function($routeProvider, routingProvider) {
      $routeProvider
        .when(routingProvider.ngGenerateShort('manager_reports_list'), {
          templateUrl: '/managerws/template/report:list.' + appVersion + '.tpl',
          controller: 'ReportListCtrl',
          resolve: {
            data: function(itemService) {
              return itemService.list('manager_ws_reports_list', {}).then(
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
