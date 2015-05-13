(function () {
  'use strict';

  angular.module('BackendApp.interceptors')
    .factory('AuthenticationIntcp', [ '$q', '$window', 'routing',
      function($q, $window, routing) {

        this.responseError = function(response) {
          if (response.status === 401) {
            var deferred = $q.defer();
            $window.location.href = routing.generate('admin_logout');
            return deferred.promise;
          }
        };

        return this;
      }
    ])

    .config(['$httpProvider', function($httpProvider) {
      $httpProvider.interceptors.push('AuthenticationIntcp');
    }]);
})();
