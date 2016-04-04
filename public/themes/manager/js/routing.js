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
            templateUrl: 'error',
          })
          .otherwise({
            redirectTo: '/404',
          });
        }
  ]);
})();