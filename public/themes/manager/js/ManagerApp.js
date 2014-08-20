
angular.module('ManagerApp', [ 'ngRoute', 'ui.bootstrap', 'ui.select2',
        'pascalprecht.translate', 'ngQuickDate', 'ngTagsInput', 'onm.routing',
        'onm.item', 'onm.messenger', 'ManagerApp.controllers'
    ]).config(function ($interpolateProvider) {
        $interpolateProvider.startSymbol('[%').endSymbol('%]');
    }).config(function ($httpProvider) {
        // Use x-www-form-urlencoded Content-Type
        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

        /**
        * The workhorse; converts an object to x-www-form-urlencoded serialization.
        * @param {Object} obj
        * @return {String}
        */
        var param = function(obj) {
        var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

        for(name in obj) {
            value = obj[name];

            if(value instanceof Array) {
                for(i=0; i<value.length; ++i) {
                    subValue = value[i];
                    fullSubName = name + '[' + i + ']';
                    innerObj = {};
                    innerObj[fullSubName] = subValue;
                    query += param(innerObj) + '&';
                }
            } else if(value instanceof Object) {
                for(subName in value) {
                    subValue = value[subName];
                    fullSubName = name + '[' + subName + ']';
                    innerObj = {};
                    innerObj[fullSubName] = subValue;
                    query += param(innerObj) + '&';
                }
            } else if(value !== undefined && value !== null)
                query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
            }

            return query.length ? query.substr(0, query.length - 1) : query;
        };

        // Override $http service's default transformRequest
        $httpProvider.defaults.transformRequest = [function(data) {
            return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
        }];
    }).config(function ($translateProvider, paginationConfig) {
        $translateProvider.translations('en', {
            Next:     'Next',
            Previous: 'Previous',
        });
        $translateProvider.translations('es', {
            Next:     'Siguiente',
            Previous: 'Anterior',
        });
        $translateProvider.translations('gl', {
            Next:     'Seguinte',
            Previous: 'Anterior',
        });

        $translateProvider.preferredLanguage('en');
    }).config(function ($routeProvider, fosJsRoutingProvider) {
        $routeProvider
            .when('/', {
                templateUrl: '/managerws/template/index:index.tpl'
            })
            .when(fosJsRoutingProvider.ngGenerateShort('/manager', 'manager_instances_list'), {
                templateUrl: '/managerws/template/instances:list.tpl',
                controller:  'InstanceListCtrl',
                resolve: {
                    data: function(itemService) {
                        return itemService.list('manager_ws_instances_list', { epp: 25 }).then(
                            function (response) {
                                return response.data;
                            }
                        );
                    }
                }
            })
            .when(fosJsRoutingProvider.ngGenerateShort('/manager', 'manager_instance_create'), {
                templateUrl: '/managerws/template/instances:edit.tpl',
                controller:  'InstanceCtrl',
                resolve: {
                    data: function($route, itemService) {
                        return itemService.new('manager_ws_instance_new').then(
                            function (response) {
                                return response.data;
                            }
                        );
                    }
                }
            })
            .when(fosJsRoutingProvider.ngGenerateShort('/manager', 'manager_instance_show', { id: '\:id' }), {
                templateUrl: '/managerws/template/instances:edit.tpl',
                controller:  'InstanceCtrl',
                resolve: {
                    data: function($route, itemService) {
                        return itemService.show('manager_ws_instance_show', $route.current.params.id).then(
                            function (response) {
                                return response.data;
                            }
                        );
                    }
                }
            })
            .when(fosJsRoutingProvider.ngGenerateShort('/manager', 'manager_framework_commands'), {
                templateUrl: '/managerws/template/framework:commands:commands.tpl',
                controller:  'CommandListCtrl',
                resolve: {
                    data: function(itemService) {
                        return itemService.list('manager_ws_commands_list', {}).then(
                            function (response) {
                                return response.data;
                            }
                        );
                    }
                }
            })
            .when(fosJsRoutingProvider.ngGenerateShort('/manager', 'manager_framework_opcache_status'), {
                templateUrl: '/managerws/template/framework:opcache_status.tpl'
            })
            .when(fosJsRoutingProvider.ngGenerateShort('/manager', 'manager_users_list'), {
                templateUrl: '/managerws/template/acl:user:list.tpl',
                controller:  'UserListCtrl',
                resolve: {
                    data: function(itemService) {
                        return itemService.list('manager_ws_users_list', {}).then(
                            function (response) {
                                return response.data;
                            }
                        );
                    }
                }
            })
            .when(fosJsRoutingProvider.ngGenerateShort('/manager', 'manager_user_show', { id: '\:id' }), {
                templateUrl: '/managerws/template/acl:user:edit.tpl',
                controller:  'UserCtrl',
                resolve: {
                    data: function($route, itemService) {
                        return itemService.show('manager_ws_user_show', $route.current.params.id).then(
                            function (response) {
                                return response.data;
                            }
                        );
                    }
                }
            })
            .when(fosJsRoutingProvider.ngGenerateShort('/manager', 'manager_user_groups_list'), {
                templateUrl: '/managerws/template/acl:user_group:list.tpl',
                controller:  'UserGroupListCtrl',
                resolve: {
                    data: function(itemService) {
                        return itemService.list('manager_ws_user_groups_list', {}).then(
                            function (response) {
                                return response.data;
                            }
                        );
                    }
                }
            })
            .when(fosJsRoutingProvider.ngGenerateShort('/manager', 'manager_user_group_show', { id: '\:id'}), {
                templateUrl: '/managerws/template/acl:user_group:edit.tpl',
                controller:  'UserGroupCtrl',
                resolve: {
                    data: function(itemService) {
                        return itemService.list('manager_ws_user_groups_list', {}).then(
                            function (response) {
                                return response.data;
                            }
                        );
                    }
                }
            })
            .otherwise({
                redirectTo: '/',
            });
    });
