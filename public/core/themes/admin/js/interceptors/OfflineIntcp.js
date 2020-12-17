(function () {
  'use strict';

  angular.module('BackendApp.interceptors')
    .factory('OfflineIntcp', [ '$q', '$rootScope',
      function($q, $rootScope) {

        this.response = function(response) {
          if (response.config.url.indexOf('/admin') !== -1) {
            $rootScope.offline = false;
          }

          return response;
        };

        this.responseError = function(response) {
          if (response.status <= 0) {
            $rootScope.offline = true;
          }

          return $q.reject(response);
        };

        return this;
      }
    ])

    .config(['$httpProvider', function($httpProvider) {
      $httpProvider.interceptors.push('OfflineIntcp');
    }]);
})();
