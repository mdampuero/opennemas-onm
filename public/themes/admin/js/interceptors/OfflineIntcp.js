(function () {
  'use strict';

  angular.module('BackendApp.interceptors')
    .factory('OfflineIntcp', [ '$q', '$rootScope', '$window',
      function($q, $rootScope, $window) {

        this.responseError = function(response) {
          if (response.status <= 0) {
            $rootScope.offline = $window.offlineMsg;
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
