(function() {
  'use strict';

  angular.module('ManagerApp')
    .config([
      '$routeProvider', 'routingProvider',
      function($routeProvider, routingProvider) {
        $routeProvider
          .when(routingProvider.ngGenerateShort('manager_onmai_prompt_list'), {
            templateUrl: '/managerws/template/onmai:prompt:list.' + appVersion + '.tpl',
            controller: 'OnmAiPromptListCtrl',
            reloadOnSearch: false
          })
          .when(routingProvider.ngGenerateShort('manager_onmai_prompt_create'), {
            templateUrl: '/managerws/template/onmai:prompt:item.' + appVersion + '.tpl',
            controller: 'OnmAiPromptCtrl',
            reloadOnSearch: false
          })
          .when(routingProvider.ngGenerateShort('manager_onmai_prompt_show', { id: ':id' }), {
            templateUrl: '/managerws/template/onmai:prompt:item.' + appVersion + '.tpl',
            controller: 'OnmAiPromptCtrl',
            reloadOnSearch: false
          })
          .when(routingProvider.ngGenerateShort('manager_onmai_instances'), {
            templateUrl: '/managerws/template/onmai:instances.' + appVersion + '.tpl',
            controller: 'OnmAiInstancesCtrl',
            reloadOnSearch: false
          })
          .when(routingProvider.ngGenerateShort('manager_onmai_config'), {
            templateUrl: '/managerws/template/onmai:config.' + appVersion + '.tpl',
            controller: 'OnmAiConfigCtrl',
            reloadOnSearch: false
          });
      }
    ]);
})();
