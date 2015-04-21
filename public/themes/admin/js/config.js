angular.module('BackendApp')
  .config(function ($interpolateProvider) {
    $interpolateProvider.startSymbol('[%').endSymbol('%]');
  }).config(function ($httpProvider) {
    // Use x-www-form-urlencoded Content-Type
    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
    $httpProvider.defaults.headers.put['Content-Type']  = 'application/x-www-form-urlencoded;charset=utf-8';
    $httpProvider.defaults.headers.patch['Content-Type']  = 'application/x-www-form-urlencoded;charset=utf-8';

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
  }).config(function (EditorProvider) {
    // Add external plugins
    EditorProvider.addExternal('imageresize', '/assets/components/imageresize/');
    EditorProvider.addExternal('wordcount', '/assets/components/wordcount/wordcount/');

    // Add custom plugins
    EditorProvider.addExternal('autokeywords', '/assets/src/ckeditor-autokeywords/');
    EditorProvider.addExternal('pastespecial', '/assets/src/ckeditor-pastespecial/');

    // Enable CKEditor for all environments (browsers)
    EditorProvider.setCompatible(true);
  });
