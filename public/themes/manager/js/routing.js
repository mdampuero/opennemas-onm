(function () {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when('/', {
            templateUrl: '/managerws/template/index:index.' + appVersion + '.tpl'
          })
          .when('/404', {
            templateUrl: '404',
          })
          .when('/403', {
            templateUrl: '403'
          })
          .otherwise({
            redirectTo: '/404',
          });
      }
  ]);
})();
