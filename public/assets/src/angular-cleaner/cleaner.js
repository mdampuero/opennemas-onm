(function () {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.cleaner
   *
   * @description
   *   description
   */
  angular.module('onm.cleaner', [])
    /**
     * @ngdoc service
     * @name  Cleaner
     *
     * @description
     *   description
     */
    .service('Cleaner', [
      function() {
        this.cleanObject = function(obj) {
          if (!obj) {
            return;
          }

          delete obj.$$hashKey;

          for (var key in obj) {
            this.clean(obj[key]);
          }
        };

        this.clean = function(e) {
          if (!e) {
            return;
          }

          if (typeof e === 'object') {
            this.cleanObject(e);
            return;
          }
        };
      }
    ]);
})();
