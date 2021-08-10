(function() {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_sitemap_show'), {
            templateUrl: '/managerws/template/sitemap:item.' + appVersion + '.tpl',
            controller: 'SitemapCtrl'
          });
      }
    ]);
})();
