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
      '$controller', '$scope', 'related', 'routing', 'translator',
      function($controller, $scope, related, routing, translator) {
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
          updateItem: 'api_v1_backend_event_update_item'
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
          return $scope.getL10nUrl(
            routing.generate($scope.routes.public, {
              slug: item.slug,
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

        $scope.getEventName = function(slug) {
          var event = Object.values($scope.data.extra.events).find(function(event) {
            return event.slug === slug;
          });

          return event ? event.name : null;
        };

        /**
         * @name $scope.$watch
         * @description
         * Watches for changes in `item.event_map_iframe` and validates whether the content
         * is an `<iframe>` with a `src` from Google Maps or OpenStreetMap.
         * If invalid, it disables the save button.
         *
         * @param {string} newValue The new value of `item.event_map_iframe`.
         */
        $scope.$watch('item.event_map_iframe', function(newValue) {
          var textarea = document.getElementById('event-map-iframe');

          if (!newValue || newValue.trim() === '') {
            $scope.isInvalidIframe = false;
            if (textarea) {
              textarea.style.border = '';
              textarea.style.backgroundColor = '';
              textarea.style.color = '';
            }
            return;
          }

          var iframeRegex = new RegExp(
            '<iframe[^>]+src=["\'](https?:\\/\\/(www\\.)?' +
            '(maps\\.google\\.com|google\\.com\\/maps|openstreetmap\\.org)[^"\']+)[^>]*><\\/iframe>'
          );

          $scope.isInvalidIframe = !iframeRegex.test(newValue);

          if (textarea) {
            textarea.style.border = $scope.isInvalidIframe ? '2px solid red' : '';
            textarea.style.backgroundColor = $scope.isInvalidIframe ? '#f8d7da' : '';
            textarea.style.color = $scope.isInvalidIframe ? '#721c24' : '';
          }
        });
      }
    ]);
})();
