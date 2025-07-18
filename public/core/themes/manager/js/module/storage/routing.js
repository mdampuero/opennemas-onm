(function() {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_storage_config'), {
            templateUrl: '/managerws/template/storage:config.' + appVersion + '.tpl',
            controller: 'StorageConfigCtrl',
            reloadOnSearch: false
          })
          .when(routingProvider.ngGenerateShort('manager_storage_tasks'), {
            templateUrl: '/managerws/template/storage:tasks.' + appVersion + '.tpl',
            controller: 'StorageTasksCtrl',
            reloadOnSearch: false
          });
      }
    ]);
})();
