(function() {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_aimodel_list'), {
            templateUrl: '/managerws/template/aimodel:list.' + appVersion + '.tpl',
            controller: 'AimodelListCtrl',
            reloadOnSearch: false
          });
      }
    ]);
})();
