
angular.module('ManagerApp', [ 'ngRoute', 'ui.bootstrap', 'ui.select2',
        'pascalprecht.translate', 'ngQuickDate', 'ngTagsInput', 'checklist-model',
        'http-interceptor', 'googlechart', 'vcRecaptcha',
        'onm.routing', 'onm.item', 'onm.messenger', 'onm.auth','onm.gravatar',
        'onm.form-autofill-fix', 'ManagerApp.controllers'
    ]).config(function ($interpolateProvider) {
        $interpolateProvider.startSymbol('[%').endSymbol('%]');
    }).config(function ($httpProvider) {
        // Use x-www-form-urlencoded Content-Type
        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
        $httpProvider.defaults.headers.post['X-App-Version'] = appVersion;

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
                        var data = {
                            orderBy: { last_login: 'desc' },
                            epp: 25
                        };

                        return itemService.list('manager_ws_instances_list', data).then(
                            function (response) {
                                return response.data;
                            }
                        );
                    }
                }
            })
            .when(fosJsRoutingProvider.ngGenerateShort('/manager', 'manager_instance_create'), {
                templateUrl: '/managerws/template/instances:item.tpl',
                controller:  'InstanceCtrl',
                resolve: {
                    data: function(itemService) {
                        return itemService.new('manager_ws_instance_new').then(
                            function (response) {
                                return response.data;
                            }
                        );
                    }
                }
            })
            .when(fosJsRoutingProvider.ngGenerateShort('/manager', 'manager_instance_show', { id: '\:id' }), {
                templateUrl: '/managerws/template/instances:item.tpl',
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
            .when(fosJsRoutingProvider.ngGenerateShort('/manager', 'manager_command_output'), {
                templateUrl: '/managerws/template/framework:commands:output.tpl',
                controller:  'CommandCtrl',
                resolve: {
                    data: function($route, itemService) {
                        return itemService.executeCommand('manager_ws_command_execute', $route.current.params.name, $route.current.params.data).then(
                            function (response) {
                                return response.data;
                            }
                        );
                    }
                }
            })
            .when(fosJsRoutingProvider.ngGenerateShort('/manager', 'manager_framework_opcache_status'), {
                templateUrl: '/managerws/template/framework:opcache_status.tpl',
                controller: 'OpcacheCtrl',
                resolve: {
                    data: function(itemService) {
                        return itemService.fetchOpcacheStatus('manager_ws_framework_opcache').then(
                            function (response) {
                                return response.data;
                            }
                        );
                    }
                }
            })
            .when(fosJsRoutingProvider.ngGenerateShort('/manager', 'manager_users_list'), {
                templateUrl: '/managerws/template/user:list.tpl',
                controller:  'UserListCtrl',
                resolve: {
                    data: function(itemService) {
                        var data = {
                            orderBy: { name: 'asc' },
                            epp: 25
                        };

                        return itemService.list('manager_ws_users_list', data).then(
                            function (response) {
                                return response.data;
                            }
                        );
                    }
                }
            })
            .when(fosJsRoutingProvider.ngGenerateShort('/manager', 'manager_user_create'), {
                templateUrl: '/managerws/template/user:item.tpl',
                controller:  'UserCtrl',
                resolve: {
                    data: function($route, itemService) {
                        return itemService.new('manager_ws_user_new').then(
                            function (response) {
                                return response.data;
                            }
                        );
                    }
                }
            })
            .when(fosJsRoutingProvider.ngGenerateShort('/manager', 'manager_user_show', { id: '\:id' }), {
                templateUrl: '/managerws/template/user:item.tpl',
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
                templateUrl: '/managerws/template/user_group:list.tpl',
                controller:  'UserGroupListCtrl',
                resolve: {
                    data: function(itemService) {
                        var data = {
                            orderBy: { name: 'asc' },
                            epp: 25
                        };

                        return itemService.list('manager_ws_user_groups_list', data).then(
                            function (response) {
                                return response.data;
                            }
                        );
                    }
                }
            })
            .when(fosJsRoutingProvider.ngGenerateShort('/manager', 'manager_user_group_create'), {
                templateUrl: '/managerws/template/user_group:item.tpl',
                controller:  'UserGroupCtrl',
                resolve: {
                    data: function(itemService) {
                        return itemService.new('manager_ws_user_group_new').then(
                            function (response) {
                                return response.data;
                            }
                        );
                    }
                }
            })
            .when(fosJsRoutingProvider.ngGenerateShort('/manager', 'manager_user_group_show', { id: '\:id'}), {
                templateUrl: '/managerws/template/user_group:item.tpl',
                controller:  'UserGroupCtrl',
                resolve: {
                    data: function($route, itemService) {
                        return itemService.show('manager_ws_user_group_show', $route.current.params.id).then(
                            function (response) {
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
    })
    .value('googleChartApiConfig', {
        version: '1',
        optionalSettings: {
            packages: ['corechart'],
            language: 'fr'
        }
    });;
