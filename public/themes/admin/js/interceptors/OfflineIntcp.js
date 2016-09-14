(function () {
  'use strict';

  angular.module('BackendApp.interceptors')
    .factory('OfflineIntcp', [ '$q', '$window', 'messenger',
      function($q, $window, messenger) {

        this.responseError = function(response) {
          console.log(response.status);
          if (response.status <= 0) {
            messenger.post({ message: $window.offlineMsg, type: 'error' });
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
