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
    }).factory('linker', function() {

      /**
       * @function get
       * @memberOf linker
       *
       * @description
       *   Returns a linker.
       *
       * @return {Object} The linker
       */
      this.get = function(keys, scope) {
        var linker = {
          /**
           * The current key name to update.
           *
           * @type {String}
           */
          key: null,

          /**
           * The list of keys that have to be updated on change.
           *
           * @type {Array}
           */
          keys: keys,

          /**
           * The localized item or list of localized items
           *
           * @type {Object}
           */
          localized: null,

          /**
           * The original item or list of original items.
           *
           * @type {Object}
           */
          original: null,

          /**
           * The scope who watches source and target.
           *
           * @type {Object}
           */
          scope: scope,

          /**
           * Links two object or two list of objects.
           *
           * @param {Object} original  The original item or list of original items.
           * @param {Object} localized The localized item or list of localized
           *                           items.
           */
          link: function(original, localized) {
            if (!this.scope) {
              return;
            }

            this.original  = original;
            this.localized = localized;

            // Link objects
            if (!angular.isArray(original) || !angular.isArray(localized)) {
              return this.linkItem(original, localized);
            }

            // Different lists' length
            if (original.length !== localized.length) {
              return;
            }

            // Link a list of objects
            for (var i = 0; i < original.length; i++) {
              this.linkItem(original[i], localized[i]);
            }
          },

          /**
           * Links two items.
           *
           * @param {Object} original  The original item.
           * @param {Object} localized The localized item.
           */
          linkItem: function(original, localized) {
            var self = this;

            // Localized changes
            this.scope.$watch(function() {
              return localized;
            }, function(nv, ov) {
              if (nv === ov) {
                return;
              }

              self.updateOriginal(original, nv);
            }, true);

            // Original changes
            this.scope.$watch(function() {
              return original;
            }, function(nv, ov) {
              if (nv === ov) {
                return;
              }

              self.updateLocalized(localized, nv);
            }, true);
          },

          /**
           * Updates the localized item or the list of localized items.
           */
          update: function() {
            if (!angular.isArray(this.original)) {
              this.updateLocalized(this.localized, this.original);
              return;
            }

            for (var i = 0; i < this.original.length; i++) {
              this.updateLocalized(this.localized[i], this.original[i]);
            }
          },

          /**
           * Updates the localized item basing on original values.
           *
           * @param {Object} localized The localized item.
           * @param {Object} original  The original item.
           */
          updateLocalized: function(localized, original) {
            for (var i = 0; i < this.keys.length; i++) {
              if (original[this.keys[i]] && original[this.keys[i]][this.key]) {
                localized[this.keys[i]] = original[this.keys[i]][this.key];
              }
            }
          },

          /**
           * Updates the original item basing on localized values.
           *
           * @param {Object} original  The original item.
           * @param {Object} localized The localized item.
           */
          updateOriginal: function(original, localized) {
            for (var i = 0; i < this.keys.length; i++) {
              if (original[this.keys[i]] &&
                  angular.isObject(original[this.keys[i]])) {
                original[this.keys[i]][this.key] = localized[this.keys[i]];
              }
            }
          },

          /**
           * Changes the key to update when target item changes.
           *
           * @param {String} key The key name.
           */
          setKey: function(key) {
            this.key = key;
          }
        };

        return linker;
      };

      return this;
    });
})();
