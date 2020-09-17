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
         * @memberOf OpinionCtrl
         *
         * @description
         *  The photo1 object.
         *
         * @type {Object}
         */
        $scope.photo1 = null;

        /**
         * @memberOf OpinionCtrl
         *
         * @description
         *  The photo2 object.
         *
         * @type {Object}
         */
        $scope.photo2 = null;

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

          var img1 = $scope.data.item.related_contents.filter(function(e) {
            return parseInt(e.pk_photo) === parseInt($scope.item.img1);
          }).shift();

          if (img1) {
            $scope.photo1 = img1;
          }

          var img2 = $scope.data.item.related_contents.filter(function(e) {
            return parseInt(e.pk_photo) === parseInt($scope.item.img2);
          }).shift();

          if (img2) {
            $scope.photo2 = img2;
          }

          var coverId = $scope.data.item.related_contents.filter(function(e) {
            return e.type === 'cover';
          }).shift();

          if (coverId) {
            $scope.cover =
              $scope.data.extra.related_contents[coverId.target_id];
          }
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
         * Updates scope when photo1 changes.
         *
         * @param array nv The new values.
         * @param array ov The old values.
         */
        $scope.$watch('photo1', function(nv, ov) {
          if (angular.equals(nv, ov)) {
            return;
          }

          if (!nv) {
            $scope.item.img1        = null;
            $scope.item.img1_footer = null;
            return;
          }

          if (!$scope.item.id ||
              parseInt($scope.item.img1) !== parseInt(nv.pk_photo)) {
            $scope.item.img1        = nv.pk_photo;
            $scope.item.img1_footer = nv.description;

            if (angular.equals(ov, $scope.photo2)) {
              $scope.photo2 = nv;
            }
          }
        }, true);

        /**
         * Updates scope when photo2 changes.
         *
         * @param array nv The new values.
         * @param array ov The old values.
         */
        $scope.$watch('photo2', function(nv, ov) {
          if (angular.equals(nv, ov)) {
            return;
          }

          if (!nv) {
            $scope.item.img2        = null;
            $scope.item.img2_footer = null;
            return;
          }

          if (!$scope.item.id ||
              parseInt($scope.item.img2) !== parseInt(nv.pk_photo)) {
            $scope.item.img2        = nv.pk_photo;
            $scope.item.img2_footer = nv.description;
          }
        }, true);

        // Update slug when title is updated
        $scope.$watch('cover', function(nv) {
          $scope.item.related_contents = [];

          if (!nv) {
            return;
          }

          $scope.item.related_contents.push({
            caption: null,
            content_type_name: nv.content_type_name,
            position: 0,
            target_id: nv.pk_content,
            type: 'cover'
          });
        }, true);
      }
    ]);
})();
