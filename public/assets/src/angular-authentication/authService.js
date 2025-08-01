(function () {
  'use strict';

  /**
   * Service to implement common actions related to item.
   *
   * @param Object $http     Http service
   * @param Object $location Location service
   * @param Object $uibModal    Modal service
   * @param Object routing   Onm routing service.
   *
   * @return Object The item service.
   */
  angular.module('onm.auth', []).factory('authService', [
    '$http', '$location', '$uibModal', 'routing', 'vcRecaptchaService',
    function ($http, $location, $uibModal, routing, vcRecaptchaService) {
      /**
       * The item service.
       *
       * @type Object
       */
      var authService = {};

      /**
       * Checks if there is an authenticated user logged in the system.
       *
       * @param string route The route name.
       *
       * @return Object The response object.
       */
      authService.isAuthenticated = function(route, name) {
          var url = routing.generate(route);

          return $http.post(url).success(function (response) {
              return response;
          });
      };

      /**
       * Logs an user in the system.
       *
       * @param string  route    The route name.
       * @param integer data     The login data.
       * @param integer attempts The number of failed login attempts.
       *
       * @return Object The response object.
       */
      authService.login = function (route, data, attempts) {
          if (data._password && data._password.indexOf('md5:') === -1) {
              data._password = 'md5:' + hex_md5(data._password);
          }

          if (attempts > 2) {
              var recaptcha      = vcRecaptchaService.data();
              data.response  = recaptcha.response;
              data.challenge = recaptcha.challenge;
          }

          var url = routing.generate(route);

          return $http.post(url, data, { ignoreAuthModule: true }).then(function (response) {
              if (!response.data.success && attempts > 2) {
                  vcRecaptchaService.reload();
              }

              return response;
          });
      };

      /**
       * Deletes a plugin given its id.
       *
       * @param string route    The route name.
       * @param array  selected The selected items.
       *
       * @return Object The response object.
       */
      authService.logout = function (route) {
          var url  = routing.generate(route);

          return $http.post(url).success(function (response) {
              return response;
          });
      };

      return authService;
    }
  ]);
})();
