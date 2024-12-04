(function() {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_prompt_list'), {
            templateUrl: '/managerws/template/prompt:list.' + appVersion + '.tpl',
            controller: 'PromptListCtrl',
            reloadOnSearch: false
          })
          .when(routingProvider.ngGenerateShort('manager_prompt_create'), {
            templateUrl: '/managerws/template/prompt:item.' + appVersion + '.tpl',
            controller: 'PromptCtrl',
            reloadOnSearch: false
          })
          .when(routingProvider.ngGenerateShort('manager_prompt_show', { id: ':id' }), {
            templateUrl: '/managerws/template/prompt:item.' + appVersion + '.tpl',
            controller: 'PromptCtrl',
            reloadOnSearch: false
          });
      }
    ]);
})();
