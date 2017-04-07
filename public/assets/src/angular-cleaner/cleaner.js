(function () {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.cleaner
   *
   * @description
   *   The `onm.cleaner` module provides a service to clean properties added to
   *   objects by some components.
   */
  angular.module('onm.cleaner', [])
    /**
     * @ngdoc service
     * @name  cleaner
     *
     * @description
     *   Service to remove $$hasKey properties from objects used inside
     *   ng-repeat.
     */
    .service('cleaner', [
      function() {
        /**
         * @function cleanObject
         * @memberOf cleaner
         *
         * @description
         *   Removes $$hashKey properties from object.
         *
         * @param {Object} obj The object to clean.
         */
        this.cleanObject = function(obj) {
          if (!obj) {
            return;
          }

          delete obj.$$hashKey;

          for (var key in obj) {
            // Convert undefined to null
            if (typeof obj[key] === 'undefined') {
              obj[key] = null
            }

            this.clean(obj[key]);
          }
        };

        /**
         * @function clean
         * @memberOf cleaner
         *
         * @description
         *   Cleans an item.
         *
         * @param {Mixed} e The item to clean
         *
         * @return {Mixed} The cleaned item.
         */
        this.clean = function(e) {
          if (typeof e === 'object') {
            this.cleanObject(e);
          }

          return e;
        };
      }
    ]);
})();
