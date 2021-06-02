(function() {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_sitemap_list'), {
            templateUrl: '/managerws/template/sitemap:list.' + appVersion + '.tpl',
            controller: 'SitemapListCtrl',
            reloadOnSearch: false
          });
      }
    ]);
})();
