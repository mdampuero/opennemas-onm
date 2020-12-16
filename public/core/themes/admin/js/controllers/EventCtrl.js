(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  EventCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires routing
     *
     * @description
     *   Check billing information when saving user.
     */
    .controller('EventCtrl', [
      '$controller', '$scope', 'linker', 'localizer', 'routing',
      function($controller, $scope, linker, localizer, routing) {
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf EventCtrl
         *
         * @description
         *  The cover object.
         *
         * @type {Object}
         */
        $scope.item = {
          body: '',
          content_type_name: 'event',
          fk_content_type: 5,
          content_status: 0,
          description: '',
          favorite: 0,
          frontpage: 0,
          created: new Date(),
          starttime: null,
          endtime: null,
          thumbnail: null,
          title: '',
          type: 0,
          with_comment: 0,
          categories: [ null ],
          related_contents: [],
          tags: [],
          event_start_date: null,
          event_end_date: null,
          event_start_hour: null,
          event_end_hour: null,
          event_place: null,
          external_link: '',
        };

        /**
         * @memberOf EventCtrl
         *
         * @description
         *  The map for related contents.
         *
         * @type {Object}
         */
        $scope.relatedMap = {
          featured_frontpage: {
            name:        'featuredFrontpage',
            replicateOn: 'featured_inner',
            simple:      true
          },
          featured_inner: {
            name:   'featuredInner',
            simple: true
          },
        };

        /**
         * @memberOf EventCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          createItem: 'api_v1_backend_event_create_item',
          getItem:    'api_v1_backend_event_get_item',
          list:       'backend_events_list',
          public:     'frontend_event_show',
          redirect:   'backend_event_show',
          saveItem:   'api_v1_backend_event_save_item',
          updateItem: 'api_v1_backend_event_update_item'
        };

        /**
         * @function buildRelated
         * @memberOf EventCtrl
         *
         * @description
         *   Initializes the scope with the list of related contents and
         *   defines watchers to update the model on changes.
         */
        $scope.buildRelated = function() {
          $scope.item.related_contents = [];

          for (var i = 0; i < $scope.data.item.related_contents.length; i++) {
            var related = $scope.data.item.related_contents[i];

            $scope.item.related_contents.push($scope.localizeRelated(related, i));

            var simple = $scope.relatedMap[related.type].simple;
            var name   = $scope.relatedMap[related.type].name;
            var item   = $scope.data.extra.related_contents[related.target_id];

            if (!simple) {
              if (!$scope.name) {
                $scope[name] = [];
              }

              $scope[name].push(item);
              continue;
            }

            $scope[name] = item;
          }

          // Updates related contents after insertion via content picker
          $scope.$watch('[ featuredFrontpage, featuredInner ]', function(nv, ov) {
            for (var i = 0; i < nv.length; i++) {
              var type = Object.keys($scope.relatedMap)[i];

              if (angular.equals(nv[i], ov[i])) {
                continue;
              }

              var caption     = null;
              var removedItem = null;

              if ($scope.relatedMap[type].simple) {
                if (ov[i]) {
                  // Try to keep caption from old item
                  removedItem = $scope.data.item.related_contents.filter(function(e) {
                    return e.type === type;
                  }).shift();

                  caption     = removedItem ? removedItem.caption : null;
                  removedItem = removedItem ?
                    $scope.data.extra.related_contents[removedItem.target_id] :
                    null;
                }

                // Remove from unlocalized
                $scope.data.item.related_contents =
                  $scope.data.item.related_contents.filter(function(e) {
                    return e.type !== type;
                  });

                // Remove from localized
                $scope.item.related_contents =
                  $scope.item.related_contents.filter(function(e) {
                    return e.type !== type;
                  });
              }

              if (!nv[i]) {
                continue;
              }

              var items = $scope.relatedMap[type].simple ? [ nv[i] ] : nv[i];

              for (var j = 0; j < items.length; j++) {
                // Add content to map of contents
                if (!$scope.data.extra.related_contents[items[j].pk_content]) {
                  $scope.data.extra.related_contents[items[j].pk_content] = items[j];
                }

                /**
                 * Override caption when adding new item or when caption matches
                 * the removed item description
                 */
                if (!removedItem || removedItem.description === caption) {
                  caption = items[j].description;
                }

                var related = {
                  caption:           caption,
                  content_type_name: items[j].content_type_name,
                  position:          j,
                  target_id:         items[j].pk_content,
                  type:              type
                };

                $scope.data.item.related_contents.push(related);
                $scope.item.related_contents.push($scope.localizeRelated(related, j));
              }

              // Copy current item to another item
              if ($scope.relatedMap[type].replicateOn) {
                var replicated = $scope.relatedMap[$scope.relatedMap[type].replicateOn];

                if (!$scope[replicated.name] ||
                    angular.equals($scope[replicated.name], ov[i])) {
                  $scope[replicated.name] = angular.copy(nv[i]);
                }
              }
            }
          }, true);
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.localize($scope.data.item, 'item', true, [ 'related_contents' ]);

          // Check if item is new (created) or existing for use default value or not
          if (!$scope.data.item.pk_content) {
            $scope.item.with_comment = $scope.data.extra.comments_enabled ? 1 : 0;
          }

          $scope.buildRelated();
        };

        /**
         * @function getFrontendUrl
         * @memberOf EventCtrl
         *
         * @description
         *   Generates the public URL basing on the item.
         *
         * @param {String} item  The item to generate route for.
         *
         * @return {String} The URL for the content.
         */
        $scope.getFrontendUrl = function(item) {
          return $scope.getL10nUrl(
            routing.generate($scope.routes.public, {
              slug: item.slug,
            })
          );
        };

        /**
         * @function getRelated
         * @memberOf EventCtrl
         *
         * @description
         *   Returns the related content based on the type.
         *
         * @param {String} type The related type.
         *
         * @return {Object} The related content.
         */
        $scope.getRelated = function(type) {
          for (var i = 0; i < $scope.item.related_contents.length; i++) {
            if ($scope.item.related_contents[i].type === type) {
              return $scope.item.related_contents[i];
            }
          }

          return null;
        };

        /**
         * @function localizeRelated
         * @memberOf EventCtrl
         *
         * @description
         *   Localizes a related content.
         *
         * @param {Object} original The content to localize.
         *
         * @return {Object} The localized content.
         */
        $scope.localizeRelated = function(original, index) {
          var localized = localizer.get($scope.config.locale).localize(original,
            [ 'caption' ], $scope.config.locale);

          // Initialize linker
          delete $scope.config.linkers[index];
          $scope.config.linkers[index] = linker.get([ 'caption' ],
            $scope.config.locale.default, $scope, true);

          // Link original and localized items
          $scope.config.linkers[index].setKey($scope.config.locale.selected);
          $scope.config.linkers[index].link(original, localized);

          return localized;
        };
      }
    ]);
})();
