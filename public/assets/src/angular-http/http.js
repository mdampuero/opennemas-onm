(function () {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.http
   *
   * @requires onm.routing
   *
   * @description
   *   The `onm.http` module provides a service to symplify HTTP requests when
   *   using Symfony with FOSJsRoutingBundle.
   */
  angular.module('orm.http', [ 'onm.routing' ])
    /**
     * @ngdoc Service
     * @name  http
     *
     * @requires $http
     * @requires routing
     *
     * @description
     *   The `http` service is a wrapper for angular $http service that generates
     *   URLs basing on Symfony routes.
     */
    .service('http', [
      '$http', 'routing',
      function($http, routing) {
        /**
         * @function delete
         * @memberOf http
         *
         * @description
         *   Executes a DELETE request.
         *
         * @param {Object} route The request route.
         * @param {Object} data  The request body.
         *
         * @return {Object} The response object.
         */
        this.delete = function(route, data) {
          if (data) {
            return this.mDelete(route, data);
          }

          return $http.delete(this.getUrl(route));
        };

        /**
         * @function get
         * @memberOf http
         *
         * @description
         *   Executes a GET request.
         *
         * @param {Object} route The request route.
         *
         * @return {Object} The response object.
         */
        this.get = function(route) {
          return $http.get(this.getUrl(route));
        };

        /**
         * @function mDelete
         * @memberOf http
         *
         * @description
         *   Executes a DELETE for multiple resources.
         *
         * @param {Object} route The request route.
         * @param {Object} data  The request body.
         *
         * @return {Object} The response object.
         */
        this.mDelete = function(route, data) {
          return $http({
            headers: {
              'Content-type': 'application/x-www-form-urlencoded;charset=utf-8'
            },
            data: data,
            method: 'delete',
            url: this.getUrl(route),
          });
        };

        /**
         * @function patch
         * @memberOf http
         *
         * @description
         *   Executes a PATCH request.
         *
         * @param {Object} route The request route.
         * @param {Object} data  The request body.
         *
         * @return {Object} The response object.
         */
        this.patch = function(route, data) {
          return $http.patch(this.getUrl(route), data);
        };

        /**
         * @function post
         * @memberOf http
         *
         * @description
         *   Executes a POST request.
         *
         * @param {Object} route The request route.
         * @param {Object} data  The request body.
         *
         * @return {Object} The response object.
         */
        this.post = function(route, data) {
          return $http.post(this.getUrl(route), data);
        };

        /**
         * @function put
         * @memberOf http
         *
         * @description
         *   Executes a PUT request.
         *
         * @param {Object} route The request route.
         * @param {Object} data  The request body.
         *
         * @return {Object} The response object.
         */
        this.put = function(route, data) {
          return $http.put(this.getUrl(route), data);
        };

        /**
         * @function getUrl
         * @memberOf http
         *
         * @description
         *   Generates an URL basing on Symfony routes.
         *
         * @param {Object} route The route.
         *
         * @return {String} The generated URL.
         */
        this.getUrl = function(route) {
          if (!(route instanceof 'Object')) {
            route = { name: route, params: {} };
          }

          return routing.generate(route.name, route.params);
        };
      }
    ]);
})();
