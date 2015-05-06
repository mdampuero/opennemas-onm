angular.module('ManagerApp')
  .config(function($routeProvider, routingProvider) {
    $routeProvider
      .when('/', {
        templateUrl: '/managerws/template/index:index.' + appVersion + '.tpl'
      })
      .when(routingProvider.ngGenerateShort('manager_instances_list'), {
        templateUrl: '/managerws/template/instances:list.' + appVersion + '.tpl',
        controller: 'InstanceListCtrl',
        resolve: {
          data: function($routeParams, itemService) {
            // Default filters
            var data = {
              orderBy: [{
                name: 'last_login',
                value: 'desc'
              }],
              epp: 25
            };

            return itemService.list('manager_ws_instances_list', data).then(
              function(response) {
                return response.data;
              }
            );
          }
        },
        reloadOnSearch: false
      })
      .when(routingProvider.ngGenerateShort('manager_instance_create'), {
        templateUrl: '/managerws/template/instances:item.' + appVersion + '.tpl',
        controller: 'InstanceCtrl',
        resolve: {
          data: function(itemService) {
            return itemService.new('manager_ws_instance_new').then(
              function(response) {
                return response.data;
              }
            );
          }
        }
      })
      .when(routingProvider.ngGenerateShort('manager_instance_show', {
        id: '\:id'
      }), {
        templateUrl: '/managerws/template/instances:item.' + appVersion + '.tpl',
        controller: 'InstanceCtrl',
        resolve: {
          data: function($route, itemService) {
            return itemService.show('manager_ws_instance_show', $route.current.params.id).then(
              function(response) {
                return response.data;
              }
            );
          }
        }
      })
      .when(routingProvider.ngGenerateShort('manager_commands'), {
        templateUrl: '/managerws/template/framework:commands:commands.' + appVersion + '.tpl',
        controller: 'CommandListCtrl',
        resolve: {
          data: function(itemService) {
            return itemService.list('manager_ws_commands_list', {}).then(
              function(response) {
                return response.data;
              }
            );
          }
        }
      })
      .when(routingProvider.ngGenerateShort('manager_command_output'), {
        templateUrl: '/managerws/template/framework:commands:output.' + appVersion + '.tpl',
        controller: 'CommandCtrl',
        resolve: {
          data: function($route, itemService) {
            return itemService.executeCommand('manager_ws_command_output',
              $route.current.params.command, $route.current.params.data).then(
              function(response) {
                return response.data;
              }
            );
          }
        }
      })
      .when(routingProvider.ngGenerateShort('manager_opcache_status'), {
        templateUrl: '/managerws/template/framework:opcache_status.' + appVersion + '.tpl',
        controller: 'OpcacheCtrl',
        resolve: {
          data: function(itemService) {
            return itemService.fetchOpcacheStatus('manager_ws_opcache_status').then(
              function(response) {
                return response.data;
              }
            );
          }
        }
      })
      .when(routingProvider.ngGenerateShort('manager_reports_list'), {
        templateUrl: '/managerws/template/report:list.' + appVersion + '.tpl',
        controller: 'ReportListCtrl',
        resolve: {
          data: function(itemService) {
            return itemService.list('manager_ws_reports_list', {}).then(
              function(response) {
                return response.data;
              }
            );
          }
        },
        reloadOnSearch: false
      })
      .when(routingProvider.ngGenerateShort('manager_users_list'), {
        templateUrl: '/managerws/template/user:list.' + appVersion + '.tpl',
        controller: 'UserListCtrl',
        resolve: {
          data: function(itemService) {
            var data = {
              orderBy: [{
                name: 'name',
                value: 'asc'
              }],
              epp: 25
            };

            return itemService.list('manager_ws_users_list', data).then(
              function(response) {
                return response.data;
              }
            );
          }
        },
        reloadOnSearch: false
      })
      .when(routingProvider.ngGenerateShort('manager_user_create'), {
        templateUrl: '/managerws/template/user:item.' + appVersion + '.tpl',
        controller: 'UserCtrl',
        resolve: {
          data: function($route, itemService) {
            return itemService.new('manager_ws_user_new').then(
              function(response) {
                return response.data;
              }
            );
          }
        }
      })
      .when(routingProvider.ngGenerateShort('manager_user_show', {
        id: '\:id'
      }), {
        templateUrl: '/managerws/template/user:item.' + appVersion + '.tpl',
        controller: 'UserCtrl',
        resolve: {
          data: function($route, itemService) {
            return itemService.show('manager_ws_user_show', $route.current.params.id).then(
              function(response) {
                return response.data;
              }
            );
          }
        }
      })
      .when(routingProvider.ngGenerateShort('manager_user_groups_list'), {
        templateUrl: '/managerws/template/user_group:list.' + appVersion + '.tpl',
        controller: 'UserGroupListCtrl',
        resolve: {
          data: function(itemService) {
            var data = {
              orderBy: [{
                name: 'name',
                value: 'asc'
              }],
              epp: 25
            };

            return itemService.list('manager_ws_user_groups_list', data).then(
              function(response) {
                return response.data;
              }
            );
          }
        },
        reloadOnSearch: false
      })
      .when(routingProvider.ngGenerateShort('manager_user_group_create'), {
        templateUrl: '/managerws/template/user_group:item.' + appVersion + '.tpl',
        controller: 'UserGroupCtrl',
        resolve: {
          data: function(itemService) {
            return itemService.new('manager_ws_user_group_new').then(
              function(response) {
                return response.data;
              }
            );
          }
        }
      })
      .when(routingProvider.ngGenerateShort('manager_user_group_show', {
        id: '\:id'
      }), {
        templateUrl: '/managerws/template/user_group:item.' + appVersion + '.tpl',
        controller: 'UserGroupCtrl',
        resolve: {
          data: function($route, itemService) {
            return itemService.show('manager_ws_user_group_show', $route.current.params.id).then(
              function(response) {
                return response.data;
              }
            );
          }
        }
      })
      .when('/404', {
        templateUrl: 'error',
      })
      .otherwise({
        redirectTo: '/404',
      });
  });
