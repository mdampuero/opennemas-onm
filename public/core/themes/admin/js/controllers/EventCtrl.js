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
      '$controller', '$scope', 'related', 'routing', 'translator', 'cleaner',
      'http', '$uibModal', '$window',
      function($controller, $scope, related, routing, translator, cleaner, http,
          $uibModal, $window) {
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

        /**
         * @inheritdoc
         */
        $scope.draftEnabled = true;

        /**
         * @inheritdoc
         */
        $scope.draftKey = 'event-draft';

        /**
         * @inheritdoc
         */
        $scope.dtm = null;

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
          created: null,
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
         *  The related service.
         *
         * @type {Object}
         */
        $scope.related = related;

        /**
         * @memberOf EventCtrl
         * @type {Boolean}
         * @description
         * State of the iframe validity
         * @default false
         */
        $scope.isInvalidIframe = false;

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
          updateItem: 'api_v1_backend_event_update_item',
          savePreview: 'api_v1_backend_event_save_preview',
          getPreview: 'api_v1_backend_event_get_preview',
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.localize($scope.data.item, 'item', true, [ 'related_contents' ]);
          $scope.expandFields();
          // Use default value for new items
          if (!$scope.data.item.pk_content) {
            $scope.data.item.with_comment =
              $scope.data.extra.comments_enabled ? 1 : 0;
          }

          if ($scope.draftKey !== null && $scope.data.item.pk_content) {
            $scope.draftKey = 'event-' + $scope.data.item.pk_content + '-draft';
          }
          $scope.checkDraft();
          related.init($scope);
          related.watch();
          translator.init($scope);
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
          if (!$scope.selectedCategory || !item.pk_content) {
            return '';
          }

          return $scope.data.extra.base_url + $scope.getL10nUrl(
            routing.generate($scope.routes.public, {
              id: item.pk_content.toString().padStart(6, '0'),
              created: item.urldatetime || $window.moment(item.created).format('YYYYMMDDHHmmss'),
              slug: item.slug,
              category_slug: $scope.selectedCategory.name
            })
          );
        };

        /**
         * Parses the provided data and calculates its text complexity and word count.
         * Updates the input data with the calculated values.
         *
         * @param {Object} data - The data object to parse, which contains a `body` property with the text to analyze.
         * @param {boolean} preview - A flag that may control whether the data is in preview mode
         * (this parameter isn't used in the function but could be useful for future extensions).
         * @returns {Object} The modified data object, including the `text_complexity` and `word_count` properties.
         */
        $scope.parseData = function(data, preview) {
          var bodyComplexity = $scope.getTextComplexity(data.body);

          data.text_complexity = bodyComplexity.textComplexity;
          data.word_count = bodyComplexity.wordsCount;
          return data;
        };

        /**
         * Retrieves the name of an event based on its slug.
         *
         * @param {string} id - The id of the event to find.
         * @returns {string|null} The name of the event if found, null otherwise.
         */
        $scope.getEventName = function(id) {
          if (!$scope.data || !$scope.data.extra || !$scope.data.extra.events) {
            return null;
          }

          var event = Object.values($scope.data.extra.events).find(function(event) {
            return event.id === parseInt(id);
          });

          return event ? event.name : null;
        };

        /**
         * @name $scope.$watch
         * @description
         * Watches for changes in `item.event_map_iframe` and validates whether the content
         * is an `<iframe>` with a `src` from Google Maps or OpenStreetMap.
         *
         * @param {string} newValue The new value of `item.event_map_iframe`.
         */
        $scope.$watch('item.event_map_iframe', function(newValue) {
          if (!newValue || newValue.trim() === '') {
            $scope.isInvalidIframe = false;

            return;
          }

          var iframeRegex = new RegExp(
            '<iframe[^>]+src=["\'](https?:\\/\\/(www\\.)?' +
            '(maps\\.google\\.com|google\\.com\\/maps|openstreetmap\\.org)[^"\']+)[^>]*><\\/iframe>'
          );

          $scope.isInvalidIframe = !iframeRegex.test(newValue);
        });

        /**
         * @function preview
         * @memberOf EventCtrl
         *
         * @description
         *  Generates a preview of the event item by sending the data to the server.
         *
         * @param {Object} item - The event item to preview.
         */
        $scope.preview = function() {
          $scope.flags.http.generating_preview = true;

          // Force ckeditor
          CKEDITOR.instances.body.updateElement();
          CKEDITOR.instances.description.updateElement();

          var status = { starttime: null, endtime: null, content_status: 1 };
          var item   = Object.assign({}, $scope.data.item, status);

          if (item.tags) {
            item.tags = item.tags.filter(function(tag) {
              return Number.isInteger(tag);
            });
          }

          var data = {
            item: JSON.stringify(cleaner.clean(item)),
            locale: $scope.config.locale.selected
          };

          http.put($scope.routes.savePreview, data).then(function() {
            $uibModal.open({
              templateUrl: 'modal-preview',
              windowClass: 'modal-fullscreen',
              controller: 'ModalCtrl',
              resolve: {
                template: function() {
                  return {
                    src: routing.generate($scope.routes.getPreview)
                  };
                },
                success: function() {
                  return null;
                }
              }
            });

            $scope.flags.http.generating_preview = false;
          }, function() {
            $scope.flags.http.generating_preview = false;
          });
        };
      }
    ]);
})();
