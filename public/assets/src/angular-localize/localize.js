(function () {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.localize
   *
   * @description
   *   The `onm.localize` module provides services to localize and unlocalize
   *   contents.
   */
  angular.module('onm.localize', [])
    .service('localizer', function () {
      /**
       * @memberOf localizer
       *
       * @description
       *  The localizer configuration.
       *
       * @type {Object}
       */
      this.config = { keys: [], locale: 'en' };

      /**
       * @function configure
       * @memberOf localizer
       *
       * @description
       *   Configures the localizer.
       *
       * @param {Object} config The localizer configuration.
       */
      this.configure = function(config) {
        this.config = angular.merge({}, this.config, config);
      };

      /**
       * @function localize
       * @memberOf localizer
       *
       * @description
       *   Localizes an item or an array of items.
       *
       * @param {Object} item   An item or an array of items to localize.
       * @param {String} locale The locale to localize to.
       *
       * @return {Object} The localized item or an array of localized items.
       */
      this.localize = function(item, locale) {
        if (!angular.isArray(item)) {
          return this.localizeItem(item, locale);
        }

        var localized = [];
        for (var i = 0; i < item.length; i++) {
          localized.push(this.localizeItem(item[i], locale));
        }

        return localized;
      };

      /**
       * @function localizeItem
       * @memberOf localizer
       *
       * @description
       *   Localizes an item.
       *
       * @param {Object} item   The item to localize.
       * @param {String} locale The locale to localize to.
       *
       * @return {Object} The localized item.
       */
      this.localizeItem = function(item, locale) {
        var localized = angular.copy(item);

        for (var i = 0; i < this.config.keys.length; i++) {
          localized[this.config.keys[i]] =
            this.localizeValue(localized[this.config.keys[i]], locale);
        }

        return localized;
      };

      /**
       * @function localizeValue
       * @memberOf localizer
       *
       * @description
       *   Localizes a value.
       *
       * @param {Object} value   The value to localize.
       * @param {String} locale The locale to localize to.
       *
       * @return {String} The localized value.
       */
      this.localizeValue = function(value, locale) {
        if (!angular.isObject(value)) {
          return value;
        }

        if (value[locale]) {
          return value[locale];
        }

        for (var i = 0; i < this.config.locales.length; i++) {
          if (value[this.config.locales[i]]) {
            return value[this.config.locales[i]];
          }
        }

        return value;
      };
    });
})();
