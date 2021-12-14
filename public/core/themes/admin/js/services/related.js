angular.module('BackendApp.services', [ 'onm.localize' ])

  /**
   * @ngdoc service
   * @name  related
   *
   * @requires linker
   * @requires localizer
   *
   * @description
   *   Service to manage related contents in forms.
   */
  .service('related', [
    'linker', 'localizer',
    function(linker, localizer) {
      /**
       * @memberOf related
       *
       * @description
       *  The related service.
       *
       * @type {Object}
       */
      var related = {
        bag: {},
        map: {
          featured_frontpage: {
            mirror: 'featured_inner',
            name:   'featuredFrontpage',
            simple: true
          },
          featured_inner: {
            name:   'featuredInner',
            simple: true
          },
          photo: {
            name:   'photos',
            simple: false
          },
          related_frontpage: {
            mirror: 'related_inner',
            name:   'relatedFrontpage',
            simple: false
          },
          related_inner: {
            name:   'relatedInner',
            simple: false
          },
        },
        mirrored: {}
      };

      /**
       * @function addToBag
       * @memberOf related
       *
       * @description
       *   Adds an item or a list of items to the current bag of related items.
       *
       * @param {Object} item The item or list of items.
       */
      related.addToBag = function(item) {
        if (angular.isArray(item)) {
          for (var i = 0; i < item.length; ++i) {
            related.addToBag(item[i]);
          }

          return;
        }

        if (!related.bag[item.pk_content]) {
          related.bag[item.pk_content] = item;
        }
      };

      /**
       * @function buildRelated
       * @memberOf related
       *
       * @description
       *   Generates a new related content of a specific type based on an item.
       *
       * @param {Object} item    The item to generate related content from.
       * @param {String} type    The related content type.
       * @param {String} caption The caption to use.
       *
       * @return {Object} The related content.
       */
      related.buildRelated = function(item, type, caption) {
        return {
          caption:           caption ? caption : item.description,
          content_type_name: item.content_type_name,
          target_id:         item.pk_content,
          position:          0,
          type:              type
        };
      };

      /**
       * @function getIds
       * @memberOf related
       *
       * @description
       *   Returns the list of ids from a list of related contents.
       *
       * @param {String} name The name of the list of related contents.
       *
       * @return {Array} The list of ids.
       */
      related.getIds = function(name) {
        if (!related.scope || !related.scope[name]) {
          return [];
        }

        if (!angular.isArray(related.scope[name])) {
          return [ related.scope[name].target_id ];
        }

        return related.scope[name].map(function(e) {
          return e.target_id;
        });
      };

      /**
       * @function getRelated
       * @memberOf related
       *
       * @description
       *   Returns the related content based on the type.
       *
       * @param {String} type The related type.
       *
       * @return {Object} The related content.
       */
      related.getRelated = function(type, index) {
        var items = related.scope.data.item.related_contents.filter(function(e) {
          return e.type === type;
        });

        items.sort(function(e, f) {
          return e.position - f.position;
        });

        if (related.map[type].simple) {
          return items.length > 0 ? items[0] : null;
        }

        return angular.isDefined(index) ? items[index] : items;
      };

      /**
       * @function exportRelated
       * @memberOf related
       *
       * @description
       *  Returns an object with the related contents.
       *
       * @return {Object} An object with the related contents.
       */
      related.exportRelated = function() {
        if (!Array.isArray(related.bag)) {
          return related.bag;
        }

        var relatedObject = {};

        var filteredBag = related.bag.filter(function(item) {
          return item !== null;
        });

        filteredBag.forEach(function(item) {
          relatedObject[item.pk_content] = item;
        });

        return relatedObject;
      };

      /**
       * @function init
       * @memberOf related
       *
       * @description
       *   Initializes the scope with the related contents.
       *
       * @param {Object} scope The scope to use.
       *
       * @return {Object} The current service.
       */
      related.init = function(scope) {
        related.scope = scope;
        related.bag   = scope.data.extra.related_contents;

        var keys = [ 'config', 'data', 'target', 'value' ];

        for (var i = 0; i < keys.length; i++) {
          if (!related.scope[keys[i]]) {
            related.scope[keys[i]] = {};
          }
        }

        for (var type in related.map) {
          var name = related.map[type].name;
          var item = related.getRelated(type);

          if (!item) {
            continue;
          }

          related.scope.data[name] = item;

          if (related.map[type].simple) {
            related.scope[name] = related.localize(item, type);
            continue;
          }

          related.scope[name] = [];

          for (var i = 0; i < item.length; i++) {
            related.scope[name].push(related.localize(item[i],
              type + '-' + item[i].target_id));
          }
        }
      };

      /**
       * @function localize
       * @memberOf related
       *
       * @description
       *   Localizes an item and creates a linker to update original and
       *   localized item when something changes.
       *
       * @param {Object} item The item to localize.
       * @param {String} name The linker name.
       */
      related.localize = function(item, name) {
        var localized = localizer.get(related.scope.config.locale)
          .localize(item, [ 'caption' ], related.scope.config.locale);

        delete related.scope.config.linkers[name];

        // Initialize linker
        related.scope.config.linkers[name] = linker.get([ 'caption' ],
          related.scope.config.locale.default, related.scope, true);

        related.scope.config.linkers[name]
          .setKey(related.scope.config.locale.selected);

        // Link original and localized items
        related.scope.config.linkers[name].link(item, localized);

        // Update linker to force caption of the selected language
        related.scope.config.linkers[name].update();

        return localized;
      };

      /**
       * @function watch
       * @memberOf related
       *
       * @description
       *   Initializes watchers for every related content type.
       */
      related.watch = function() {
        related.watchData();

        for (var type in related.map) {
          related.watchValue(related.map[type].name, related.map[type].simple);
          related.watchScope(type, related.map[type].name, related.map[type].simple);

          if (related.map[type].mirror) {
            related.watchMirror(related.map[type].name,
              related.map[type].mirror, related.map[type].simple);
          }
        }
      };

      /**
       * Update related contents in item when scope changes.
       */
      related.watchData = function() {
        related.scope.$watch(function() {
          var targets = [];

          for (var type in related.map) {
            targets.push(related.scope.data[related.map[type].name]);
          }

          return targets;
        }, function(nv) {
          var types = Object.keys(related.map);

          related.scope.data.item.related_contents = [];

          for (var i = 0; i < nv.length; i++) {
            if (!nv[i]) {
              continue;
            }

            var type = types[i];

            if (related.map[type].simple) {
              related.scope.data.item.related_contents.push(nv[i]);
              continue;
            }

            related.scope.data.item.related_contents =
              related.scope.data.item.related_contents.concat(nv[i]);
          }
        }, true);
      };

      /**
       * @function watchMirror
       * @memberOf related
       *
       * @description
       *   Mirrors a related content of a type in another related content.
       *
       * @param {String}  name   The name in the scope of the related content to
       *                         mirror.
       * @param {String}  type   The type of the mirrored related content.
       * @param {Boolean} simple Whether to mirror a simple content or a list
       *                         of contents.
       *
       * @return {type} description
       */
      related.watchMirror = function(name, type, simple) {
        related.scope.$watch(name, function(nv, ov) {
          if (related.scope.item.pk_content && !ov && !nv) {
            return;
          }

          // Return if empty or item already mirrored
          if (!nv || related.mirrored[name]) {
            return;
          }

          // Copy item when mirror is empty
          if (simple && !related.scope.data[related.map[type].name]) {
            var item = angular.copy(nv);

            item.type = type;

            related.scope.data[related.map[type].name] = item;
            related.scope[related.map[type].name]      = related.localize(item, type);

            related.mirrored[name] = true;
            return;
          }

          if (nv === ov) {
            return;
          }

          if (!ov && related.scope[related.map[type].name]) {
            related.mirrored[name] = true;
            return;
          }

          var oldIds = ov.map(function(e) {
            return [ e.target_id, e.caption ];
          });

          var mirrorIds = !angular.isArray(related.scope[related.map[type].name]) ?
            [] : related.scope[related.map[type].name].map(function(e) {
              return [ e.target_id, e.caption ];
            });

          if (angular.equals(oldIds, mirrorIds)) {
            related.scope.data[related.map[type].name] = [];
            related.scope[related.map[type].name]      = [];

            for (var i = 0; i < nv.length; i++) {
              var item = angular.copy(nv[i]);

              item.type = type;

              related.scope.data[related.map[type].name].push(item);
              related.scope[related.map[type].name].push(
                related.localize(item, type + '_' + i)
              );
            }

            related.mirrored[name] = true;
          }
        }, true);
      };

      /**
       * @function watchScope
       * @memberOf related
       *
       * @description
       *   Defines a watcher to update related contents when an item is
       *   selected via content/media picker.
       *
       * @param {String}  type   The type of the related contents.
       * @param {String}  name   The name of the related contents.
       * @param {Boolean} simple Whether the related content is a single object
       *                         or an array of objects.
       */
      related.watchScope = function(type, name, simple) {
        related.scope.$watch(function() {
          return related.scope.target[name];
        }, function(nv) {
          if (!nv) {
            return;
          }

          related.addToBag(nv);

          if (simple) {
            var oldCaption = null;

            // Keep old caption if it was manually changed
            if (related.scope.data[name] &&
                related.bag[related.scope.data[name].target_id] &&
                related.bag[related.scope.data[name].target_id].description !==
                  related.scope.data[name].caption) {
              oldCaption = related.scope.data[name].caption;
            }

            var item = related.buildRelated(nv, type, oldCaption);

            related.scope.data[name]   = item;
            related.scope[name]        = related.localize(item, type);
            related.scope.target[name] = null;
            return;
          }

          for (var i = 0; i < nv.length; i++) {
            var item = related.buildRelated(nv[i], type);

            related.scope.data[name].push(item);
            related.scope[name].push(related.localize(item, type + '-' + item.target_id));
          }

          related.scope.target[name] = null;
        });
      };

      /**
       * @function watchForm
       * @memberOf related
       *
       * @description
       *   Defines a watcher to update values used in forms to support
       *   validation for related contents.
       *
       * @param {String}  name   The related content name in the scope.
       * @param {Boolean} simple Whether the related content is a single object
       *                         or an array of objects.
       */
      related.watchValue = function(name, simple) {
        related.scope.$watch(name, function(nv) {
          if (!nv) {
            related.scope.value[name] = null;
            related.scope.form.$setPristine();
            return;
          }

          related.scope.form.$setDirty();

          if (!simple) {
            related.scope.value[name] = nv.length > 0 ? nv.length : null;

            for (var i = 0; i < nv.length; ++i) {
              related.scope[name][i].position = i;
            }
          }
        }, true);
      };

      return related;
    }
  ]);
