(function(){
  'use strict';

  angular.module('BackendApp')
  .config(['$interpolateProvider', function ($interpolateProvider) {
    $interpolateProvider.startSymbol('[%').endSymbol('%]');
  }]).config(['$httpProvider', 'serializerProvider', function ($httpProvider, serializerProvider) {
    $httpProvider.defaults.headers.common['X-App-Version'] = appVersion;

    // Use x-www-form-urlencoded as Content-Type
    $httpProvider.defaults.headers.post['Content-Type']  = 'application/x-www-form-urlencoded;charset=utf-8';
    $httpProvider.defaults.headers.put['Content-Type']   = 'application/x-www-form-urlencoded;charset=utf-8';
    $httpProvider.defaults.headers.patch['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

    // Add header for XHR request
    $httpProvider.defaults.headers.put['X-Requested-With']   = 'XMLHttpRequest';
    $httpProvider.defaults.headers.post['X-Requested-With']  = 'XMLHttpRequest';
    $httpProvider.defaults.headers.patch['X-Requested-With'] = 'XMLHttpRequest';

    // Override $http service's default transformRequest
    $httpProvider.defaults.transformRequest = [function(data) {
      if (angular.isObject(data) && String(data) !== '[object File]') {
          return serializerProvider.serialize(data);
        }

        return data;
    }];
  }]).config(['$translateProvider', function ($translateProvider) {
    $translateProvider.preferredLanguage('en');
  }]).config(['$analyticsProvider', function ($analyticsProvider) {
    $analyticsProvider.virtualPageviews(false);
  }]).config(['anTinyconProvider', function(anTinyconProvider){
    anTinyconProvider.setOptions({
      width: 7,
      height: 9,
      font: '10px arial',
      colour: '#ffffff',
      fallback: true
    });
  }]).config(['EditorProvider', function (EditorProvider) {
    // Add external plugins
    EditorProvider.addExternal('imageresize', '/assets/components/imageresize/');
    EditorProvider.addExternal('wordcount', '/assets/components/wordcount/wordcount/');

    // Add custom plugins
    EditorProvider.addExternal('autokeywords', '/assets/src/ckeditor-autokeywords/');
    EditorProvider.addExternal('pastespecial', '/assets/src/ckeditor-pastespecial/');

    // Enable CKEditor for all environments (browsers)
    EditorProvider.setCompatible(true);
  }]);
})();
