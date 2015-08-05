angular.module('ManagerApp')
  .config(function ($interpolateProvider) {
    $interpolateProvider.startSymbol('[%').endSymbol('%]');
  }).config(function ($httpProvider) {
    // Use x-www-form-urlencoded Content-Type
    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
    $httpProvider.defaults.headers.put['Content-Type']  = 'application/x-www-form-urlencoded;charset=utf-8';
    $httpProvider.defaults.headers.patch['Content-Type']  = 'application/x-www-form-urlencoded;charset=utf-8';

    $httpProvider.defaults.headers.common['X-App-Version'] = appVersion;

    /**
    * The workhorse; converts an object to x-www-form-urlencoded serialization.
    * @param {Object} obj
    * @return {String}
    */
    var param = function(obj) {
      var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

      for(name in obj) {
        value = obj[name];

        if (value instanceof Object) {
          for(subName in value) {
            subValue = value[subName];
            fullSubName = name + '[' + subName + ']';
            innerObj = {};
            innerObj[fullSubName] = subValue;
            query += param(innerObj) + '&';
          }
        } else if(value instanceof Array) {
          for(i = 0; i < value.length; ++i) {
            subValue = value[i];
            fullSubName = name + '[' + i + ']';
            innerObj = {};
            innerObj[fullSubName] = subValue;
            query += param(innerObj) + '&';
          }
        } else if(value !== undefined && value !== null) {
          query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
        }
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
      Previous:   'Previous',
      FormErrors: 'There are errors in the form'
    });
    $translateProvider.translations('es', {
      Next:     'Siguiente',
      Previous:   'Anterior',
      FormErrors: 'El formulario contiene errores'
    });
    $translateProvider.translations('gl', {
      Next:     'Seguinte',
      Previous:   'Anterior',
      FormErrors: 'O formulario contén errores'
    });

    $translateProvider.preferredLanguage('en');
  }).config(function (routingProvider) {
    routingProvider.setBaseRoute('/manager');
  }).value('googleChartApiConfig', {
    version: '1',
    optionalSettings: {
      packages: ['corechart'],
      language: 'fr'
    }
  }).config(['cfpLoadingBarProvider', function(cfpLoadingBarProvider) {
    cfpLoadingBarProvider.includeSpinner = false;
  }]);;
