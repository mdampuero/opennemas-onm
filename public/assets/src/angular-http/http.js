(function() {
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
  angular.module('onm.http', [ 'onm.routing' ])

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
         * @function convertToFormData
         * @memberOf http
         *
         * @description
         *   Converts an regular Object to a FormData object.
         *
         * @param {Object}   data     The object to convert.
         * @param {Boolean}  put      Whether to append special '_method' with value
         *                            'PUT' to the FormData object.
         * @param {String}   key      The key part processed in previous calls.
         * @param {FormData} formData The formData with values procesed in
         *                            previous calls.
         *
         * @return {FormData} The FormData object.
         */
        this.convertToFormData = function(value, put, key, formData) {
          if (!formData) {
            formData = new FormData();

            if (put) {
              formData.append('_method', 'PUT');
            }
          }

          // First call or Array/Object values
          if (value instanceof HTMLCanvasElement) {
            var blob = this.dataURItoBlob(value.toDataURL('image/jpeg', 0.65));

            formData.append(key, new File([ blob ], key));
          } else if (value instanceof Object && !(value instanceof File)) {
            for (var i in value) {
              var k = i;

              if (key) {
                k = key + '[' + i + ']';
              }

              this.convertToFormData(value[i], put, k, formData);
            }

            return formData;
          }

          // Scalar or File values
          if (value && key) {
            formData.append(key, value);
          }

          return formData;
        };

        /**
         * @function dataURItoBlob
         * @memberOf http
         *
         * @description
         *   Converts datauri type to blob.
         *
         * @param {Object}   dataUri     The data tu convert to blob.
         *
         * @return {blob} The same data but in blob format
         */
        this.dataURItoBlob = function(dataURI) {
          // Convert base64 to raw binary data held in a string
          var byteString = atob(dataURI.split(',')[1]);

          // Separate out the mime component
          var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

          // Write the bytes of the string to an ArrayBuffer
          var arrayBuffer = new ArrayBuffer(byteString.length);
          var ia = new Uint8Array(arrayBuffer);

          for (var i = 0; i < byteString.length; i++) {
            ia[i] = byteString.charCodeAt(i);
          }

          var dataView = new DataView(arrayBuffer);
          var blob = new Blob([ dataView ], { type: mimeString });

          return blob;
        };

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
          if (!this.hasFile(data)) {
            return $http.post(this.getUrl(route), data);
          }

          var formData = this.convertToFormData(data);

          return $http.post(this.getUrl(route), formData, {
            transformRequest: angular.identity,
            headers: { 'Content-Type': undefined } // eslint-disable-line no-undefined
          });
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
          if (!this.hasFile(data)) {
            return $http.put(this.getUrl(route), data);
          }

          var formData = this.convertToFormData(data, true);

          return $http.post(this.getUrl(route), formData, {
            transformRequest: angular.identity,
            headers: { 'Content-Type': undefined } // eslint-disable-line no-undefined
          });
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
          if (!(route instanceof Object)) {
            route = { name: route, params: {} };
          }

          return routing.generate(route.name, route.params);
        };

        /**
         * @function hasFile
         * @memberOf http
         *
         * @description
         *   Checks if the there are files in data.
         *
         * @param {Object} data The data to check.
         *
         * @return {Boolean} True if there are files in data. False otherwise.
         */
        this.hasFile = function(data) {
          if (data instanceof File || data instanceof HTMLCanvasElement) {
            return true;
          }

          if (data instanceof Object) {
            for (var i in data) {
              if (this.hasFile(data[i])) {
                return true;
              }
            }
          }

          return false;
        };
      }
    ]);
})();
