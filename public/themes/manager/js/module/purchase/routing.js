(function () {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_purchases_list'), {
            templateUrl: '/managerws/template/purchase:list.' + appVersion + '.tpl',
            controller: 'PurchaseListCtrl',
            reloadOnSearch: false
          })
          .when(routingProvider.ngGenerateShort('manager_purchase_show', { id: '\:id' }), {
            templateUrl: '/managerws/template/purchase:item.' + appVersion + '.tpl',
            controller: 'PurchaseCtrl',
          });
      }
    ]);
})();
