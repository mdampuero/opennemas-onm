(function() {
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
    .factory('linker', function() {
      /**
       * @function get
       * @memberOf linker
       *
       * @description
       *   Returns a linker.
       *
       * @return {Object} The linker
       */
      this.get = function(keys, scope, clean, ignore) {
        return {

          /**
           * Flag to delete objects not found in original values when enabled.
           *
           * @type {Boolean}
           */
          clean: clean,

          /**
           * List of keys to ignore in this linker.
           *
           * @type {Array}
           */
          ignore: ignore || [],

          /**
           * The current key name to update.
           *
           * @type {String}
           */
          key: null,

          /**
           * The last key name.
           *
           * @type {String}
           */
          previousKey: null,

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
              this.linkItem(original, localized);
              return;
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
            var that = this;

            this.scope.$watch(function() {
              return that.key;
            }, function(nv, ov) {
              if (ov && nv && ov !== nv) {
                that.previousKey = ov;
              }
            }, true);

            // Localized changes
            this.scope.$watch(function() {
              return localized;
            }, function(nv, ov) {
              if (nv === ov) {
                return;
              }

              that.updateOriginal(original, nv);
            }, true);

            // Original changes
            this.scope.$watch(function() {
              return original;
            }, function(nv, ov) {
              if (nv === ov) {
                return;
              }

              that.updateLocalized(localized, nv);
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
            var that = this;

            for (var i = 0; i < this.keys.length; i++) {
              if (original[this.keys[i]]) {
                if (this.clean) {
                  delete localized[this.keys[i]];
                }

                if (angular.isString(original[this.keys[i]])) {
                  localized[this.keys[i]] = original[this.keys[i]];
                }

                if (original[this.keys[i]][this.key]) {
                  localized[this.keys[i]] = original[this.keys[i]][this.key];
                }
              }
            }

            var ukeys = Object.keys(original).filter(function(e) {
              return that.keys.indexOf(e) < 0 && that.ignore.indexOf(e) < 0;
            });

            for (var i = 0; i < ukeys.length; i++) {
              localized[ukeys[i]] = original[ukeys[i]];
            }
          },

          /**
           * Updates the original item basing on localized values.
           *
           * @param {Object} original  The original item.
           * @param {Object} localized The localized item.
           */
          updateOriginal: function(original, localized) {
            var that = this;

            for (var i = 0; i < this.keys.length; i++) {
              // Value missing
              if (!original[this.keys[i]]) {
                original[this.keys[i]] = {};
                continue;
              }

              if (angular.isString(original[this.keys[i]])) {
                var value = original[this.keys[i]];

                // If locale changed, convert string
                if (this.previousKey) {
                  original[this.keys[i]] = {};
                  original[this.keys[i]][this.previousKey] = value;
                  continue;
                }

                // If locale not changed, update string
                original[this.keys[i]] = localized[this.keys[i]];
                continue;
              }

              // Convert string to l10n_string
              if (angular.isObject(original[this.keys[i]])) {
                original[this.keys[i]][this.key] = localized[this.keys[i]];
              }
            }

            var ukeys = Object.keys(localized).filter(function(e) {
              return that.keys.indexOf(e) < 0 && that.ignore.indexOf(e) < 0;
            });

            for (var i = 0; i < ukeys.length; i++) {
              original[ukeys[i]] = localized[ukeys[i]];
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
      };

      return this;
    }).factory('localizer', function() {
      /**
       * @function get
       * @memberOf linker
       *
       * @description
       *   Returns a linker.
       *
       * @return {Object} The linker
       */
      this.get = function(config) {
        return {

          /**
           * The localizer configuration.
           *
           * @type {Object}
           */
          config: config,

          /**
           * Localizes an item or an array of items.
           *
           * @param {Object} item   An item or an array of items to localize.
           * @param {Array}  keys   The list of keys to localize.
           * @param {String} locale The locale to localize to.
           *
           * @return {Object} The localized item or an array of localized items.
           */
          localize: function(item, keys, locale) {
            if (!angular.isArray(item)) {
              return this.localizeItem(item, keys, locale);
            }

            var localized = [];

            for (var i = 0; i < item.length; i++) {
              localized.push(this.localizeItem(item[i], keys, locale));
            }

            return localized;
          },

          /**
           * Localizes an item.
           *
           * @param {Object} item   The item to localize.
           * @param {Array}  keys   The list of keys to localize.
           * @param {String} locale The locale to localize to.
           *
           * @return {Object} The localized item.
           */
          localizeItem: function(item, keys, locale) {
            var localized = angular.copy(item);

            for (var i = 0; i < keys.length; i++) {
              if (!angular.isDefined(localized[keys[i]])) {
                continue;
              }

              localized[keys[i]] =
                this.localizeValue(localized[keys[i]], locale);
            }

            return localized;
          },

          /**
           * @function localizeValue
           * @memberOf localizer
           *
           * @description
           *   Localizes a value.
           *
           * @param {Object} value  The value to localize.
           * @param {String} locale The locale to localize to.
           *
           * @return {String} The localized value.
           */
          localizeValue: function(value, locale) {
            if (angular.isString(value) || !angular.isObject(value)) {
              return value;
            }

            if (angular.isDefined(value[locale]) && value[locale] !== '') {
              return value[locale];
            }

            for (var key in this.config.available) {
              if (value[key] && value[key] !== '') {
                return value[key];
              }
            }

            return value;
          }
        };
      };

      return this;
    });
})();
