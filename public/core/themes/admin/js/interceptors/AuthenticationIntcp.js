(function() {
  'use strict';

  angular.module('BackendApp.interceptors')
    .factory('AuthenticationIntcp', [
      '$q', '$window', 'routing',
      function($q, $window, routing) {
        this.responseError = function(response) {
          if (response.status === 401) {
            $window.location.href = routing.generate('core_authentication_logout');
            return;
          }

          return $q.reject(response);
        };

        return this;
      }
    ])

    .config([
      '$httpProvider',
      function($httpProvider) {
        $httpProvider.interceptors.push('AuthenticationIntcp');
      }
    ]);
})();
