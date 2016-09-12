(function () {
  'use strict';

  angular.module('onm.security', [])
    .service('security', [
      function() {
        /**
         * @memberOf security
         *
         * @description
         *  The list of categories.
         *
         * @type {Array}
         */
        this.categories  = [];

        /**
         * @memberOf security
         *
         * @description
         *  The list of extensions.
         *
         * @type {Array}
         */
        this.extensions = [];

        /**
         * @memberOf security
         *
         * @description
         *  The current instance.
         *
         * @type {Object}
         */
        this.instance = null;

        /**
         * @memberOf security
         *
         * @description
         *  The list of instances.
         *
         * @type {Array}
         */
        this.instances = [];

        /**
         * @memberOf security
         *
         * @description
         *  The list of permissions.
         *
         * @type {Array}
         */
        this.permissions = [];

        /**
         * @memberOf security
         *
         * @description
         *  The JWT token.
         *
         * @type {String}
         */
        this.token = null;

        /**
         * @memberOf security
         *
         * @description
         *  The current user.
         *
         * @type {Object}
         */
        this.user = null;

        /**
         * @function canEnable
         * @memberOf security
         *
         * @description
         *  Checks if the current user can enable the item basing on a extension
         *  UUID.
         *
         * @param {String} uuid The extensions UUID.
         *
         * @return {Boolean} True if the user can enable the item basing on the
         *                   extension UUID. False otherwise.
         */
        this.canEnable = function(uuid) {
          if (this.hasPermission('MASTER')) {
            return true;
          }

          if (!this.user || !this.user.extensions) {
            return false;
          }

          return this.user.extensions.indexOf(uuid) !== -1;
        };

        /**
         * @function hasCategory
         * @memberOf security
         *
         * @description
         *   Checks if the category is allowed.
         *
         * @param {Integer} category The category id.
         *
         * @return {Boolean} True if the category is allowed. False otherwise.
         */
        this.hasCategory = function(category) {
          return this.categories.indexOf(category) !== -1;
        };

        /**
         * @function hasExtension
         * @memberOf security
         *
         * @description
         *  Checks if the extension is enabled.
         *
         * @param {String} uuid The extension to check.
         *
         * @return {Boolean} True if the extensionis enabled. False otherwise.
         */
        this.hasExtension = function(uuid) {
          if (this.hasPermission('MASTER')) {
            return true;
          }

          return this.instance.activated_modules.indexOf(uuid) !== -1;
        };

        /**
         * @function hasInstance
         * @memberOf security
         *
         * @description
         *   Checks if the user owns the instance.
         *
         * @param {String} instance The instance name.
         *
         * @return {Boolean} True if the user owns the instance. False
         *                   otherwise.
         */
        this.hasInstance = function (instance) {
          if (this.hasPermission('MASTER')) {
            return true;
          }

          return this.instances.indexOf(instance) !== -1;
        };

        /**
         * @function hasPermission
         * @memberOf security
         *
         * @description
         *   Checks if the permission is granted.
         *
         * @param {} instance The instance name.
         *
         * @return {Boolean} True if the user owns the instance. False
         *                   otherwise.
         */
        this.hasPermission = function(permission) {
          if (this.permissions.indexOf('MASTER') !== -1) {
            return true;
          }

          return this.permissions.indexOf(permission) !== -1;
        };

        /**
         * @function reset
         * @memberOf security
         *
         * @description
         *   Resets all values for security.
         */
        this.reset = function () {
          this.categories  = [];
          this.extensions  = [];
          this.instances   = [];
          this.permissions = [];
          this.token       = null;
          this.user        = null;
        };
      }
    ]);
})();
