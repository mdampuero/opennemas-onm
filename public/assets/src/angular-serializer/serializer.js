(function() {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.serialize
   *
   * @description
   *   The `onm.serialize` module provides a service to serialize objects.
   */
  angular.module('onm.serializer', [])

    /**
     * @ngdoc provider
     * @name  serializer
     *
     * @description
     *   Service to serialize objects.
     */
    .provider('serializer', [
      '$windowProvider',
      function($windowProvider) {
        /**
         * Converts an object to x-www-form-urlencoded serialization.
         *
         * @param {mixed} value The value to tranform.
         *
         * @return {String} The transformed object.
         */
        this.serialize = function(value, key) {
          if (!(value instanceof Array) && !(value instanceof Object)) {
            var query = key + '=';

            if (value !== 'undefined' && value !== null) {
              query += encodeURIComponent(value);
            }

            return query;
          }

          if (value instanceof Date) {
            return key + '=' + $windowProvider.$get().moment(value)
              .format('YYYY-MM-DD HH:mm:ss');
          }

          var query = '';

          for (var i in value) {
            query += this.serialize(value[i], key ? key + '[' + i + ']' : i) + '&';
          }

          // Empty Array or Object
          if (query === '') {
            return key + '=';
          }

          return query.substring(0, query.length - 1);
        };

        /**
         * Returns the current service.
         *
         * @return Object The current object.
         */
        this.$get = function() {
          return this;
        };
      }
    ]);
})();
