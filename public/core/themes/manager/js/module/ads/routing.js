(function() {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_ads_list'), {
            templateUrl: '/managerws/template/ads:list.' + appVersion + '.tpl',
            controller: 'AdsListCtrl',
            reloadOnSearch: false
          })
          .when(routingProvider.ngGenerateShort('manager_ads_create'), {
            templateUrl: '/managerws/template/ads:item.' + appVersion + '.tpl',
            controller: 'AdsCtrl',
            reloadOnSearch: false
          })
          .when(routingProvider.ngGenerateShort('manager_ads_show', { id: ':id' }), {
            templateUrl: '/managerws/template/ads:item.' + appVersion + '.tpl',
            controller: 'AdsCtrl',
            reloadOnSearch: false
          });
      }
    ]);
})();
