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
      '$controller', '$scope', 'routing',
      function($controller, $scope, routing) {
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
          $scope.localize($scope.data.item, 'item', true);

          // Check if item is new (created) or existing for use default value or not
          if (!$scope.data.item.pk_content) {
            $scope.item.with_comment = $scope.data.extra.comments_enabled ? 1 : 0;
          }

          var featuredFrontpage = $scope.data.item.related_contents.filter(function(e) {
            return e.type === 'featured_frontpage';
          }).shift();

          var featuredInner = $scope.data.item.related_contents.filter(function(e) {
            return e.type === 'featured_inner';
          }).shift();

          if (featuredFrontpage) {
            $scope.featuredFrontpage =
              $scope.data.extra.related_contents[featuredFrontpage.target_id];
          }

          if (featuredInner) {
            $scope.featuredInner =
              $scope.data.extra.related_contents[featuredInner.target_id];
          }
        };

        $scope.getPosition = function(relation) {
          var position = -1;

          for (var i = 0; i < $scope.item.related_contents.length; i++) {
            if ($scope.item.related_contents[i].type === relation) {
              position = i;
            }
          }

          return position;
        };

        $scope.getCaption = function(relation, nv, ov) {
          var featured = $scope.item.related_contents.filter(function(e) {
            return e.type === relation;
          })[0];

          var oldCaption = !featured ?
            null :
            featured.caption;

          var ovCaption = !ov ? null : ov.description;

          if (oldCaption && !ovCaption) {
            return oldCaption;
          }

          if (ovCaption && ovCaption !== oldCaption) {
            return oldCaption;
          }

          return nv.description;
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

        $scope.$watch('featuredFrontpage', function(nv, ov) {
          if (!nv) {
            return;
          }

          var caption = $scope.getCaption('featured_frontpage', nv, ov);

          $scope.item.related_contents = $scope.item.related_contents.filter(function(e) {
            return e.type !== 'featured_frontpage';
          });

          $scope.item.related_contents.push({
            caption: caption,
            content_type_name: nv.content_type_name,
            position: 0,
            target_id: nv.pk_content,
            type: 'featured_frontpage'
          });

          if (!$scope.featuredInner) {
            $scope.featuredInner = $scope.featuredFrontpage;
          }
        }, true);

        $scope.$watch('featuredInner', function(nv, ov) {
          if (!nv) {
            return;
          }

          var caption = $scope.getCaption('featured_inner', nv, ov);

          $scope.item.related_contents = $scope.item.related_contents.filter(function(e) {
            return e.type !== 'featured_inner';
          });

          $scope.item.related_contents.push({
            caption: caption,
            content_type_name: nv.content_type_name,
            position: 0,
            target_id: nv.pk_content,
            type: 'featured_inner'
          });
        }, true);
      }
    ]);
})();
