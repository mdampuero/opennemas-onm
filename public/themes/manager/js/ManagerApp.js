
angular.module('ManagerApp', [ 'ngRoute', 'ui.bootstrap', 'pascalprecht.translate',
        'onm.routing', 'onm.item', 'ManagerApp.controllers'

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
            .when(fosJsRoutingProvider.generate('manager_instance_list'), {
                templateUrl: '/managerws/template/instances:list.tpl',
                controller:  'InstanceCtrl',
                resolve: {
                    list: function(itemService) {
                        return itemService.list('manager_ws_instances_list', {}).then(
                            function (response) {
                                return response.data;
                            }
                        );
                    }
                }
            })
            .when(fosJsRoutingProvider.generate('manager_framework_commands'), {
                templateUrl: '/managerws/template/framework:commands:commands.tpl',
                controller:  'CommandCtrl',
                resolve: {
                    list: function(itemService) {
                        return itemService.list('manager_ws_commands_list', {}).then(
                            function (response) {
                                return response.data;
                            }
                        );
                    }
                }
            })
            .when(fosJsRoutingProvider.generate('manager_framework_opcache_status'), {
                templateUrl: '/managerws/template/framework:opcache_status.tpl'
            })
            .when(fosJsRoutingProvider.generate('manager_user_list'), {
                templateUrl: '/managerws/template/acl:user:list.tpl'
            })
            .when(fosJsRoutingProvider.generate('manager_usergroup_list'), {
                templateUrl: '/managerws/template/acl:user_group:list.tpl'
            })
            .otherwise({
                redirectTo: '/',
            });
    });
