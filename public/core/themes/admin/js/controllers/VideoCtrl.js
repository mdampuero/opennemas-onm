/**
 * Handle actions for video inner form.
 */
angular.module('BackendApp.controllers').controller('VideoCtrl', [
  '$controller', '$scope', '$timeout', '$window', 'http', 'routing', 'messenger',
  function($controller, $scope, $timeout, $window, http, routing, messenger) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

    /**
     * @memberOf VideoCtrl
     *
     * @description
     *  The list of external video properties.
     *
     * @type {Array}
     */
    $scope.information = {};

    /**
     * @memberOf VideoCtrl
     *
     * @description
     *  The item object.
     *
     * @type {Object}
     */
    $scope.item = {
      body: '',
      content_type_name: 'video',
      fk_content_type: 9,
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
      categories: [],
      related_contents: [],
      tags: [],
      external_link: '',
      path: '',
      information: {}
    };

    /**
     * @memberOf VideoCtrl
     *
     * @description
     *   Object with trusted URLs for preview.
     *
     * @type {Object}
     */
    $scope.preview = {};

    /**
     * @memberOf VideoCtrl
     *
     * @description
     *  The list of routes for the controller.
     *
     * @type {Object}
     */
    $scope.routes = {
      createItem: 'api_v1_backend_video_create_item',
      getItem:    'api_v1_backend_video_get_item',
      list:       'backend_videos_list',
      public:     'frontend_video_show',
      redirect:   'backend_video_show',
      saveItem:   'api_v1_backend_video_save_item',
      updateItem: 'api_v1_backend_video_update_item'
    };

    /**
     * @inheritdoc
     */
    $scope.buildScope = function() {
      switch ($scope.data.item.type) {
        case 'script':
          $scope.setType('script');
          break;

        case 'external':
          $scope.setType('external');

          var info = $scope.data.item.information.source;

          $scope.html = info.flv ? 'flv' : 'html5';
          break;

        default:
          if ($scope.data.item.path) {
            $scope.setType('web-source');
          }
          break;
      }

      $scope.localize($scope.data.item, 'item', true);

      // Check if item is new (created) or existing for use default value or not
      if (!$scope.data.item.pk_content) {
        $scope.item.with_comment = $scope.data.extra.comments_enabled ? 1 : 0;
      }

      if ($scope.item.related_contents.length > 0) {
        var cover = $scope.data.extra.related_contents[$scope.item.related_contents[0].target_id];
      }

      if (cover) {
        $scope.cover = cover;
      }
    };

    /**
     * @function setType
     * @memberOf VideoCtrl
     *
     * @description
     *   Updates the scope to the proper video type.
     */
    $scope.setType = function(type) {
      if (!type) {
        return;
      }

      if (type === 'external' || type === 'script') {
        $scope.item.type = type;
      }

      if (!$scope.item.type) {
        $scope.item.type = 'html5';
      }

      $scope.type               = type;
      $scope.flags.visible.grid = true;
    };

    /**
     * @function getVideoData
     * @memberOf VideoCtrl
     *
     * @description
     *   Gets the video information from the external service.
     */
    $scope.getVideoData = function() {
      var route = {
        name:   'api_v1_backend_video_get_info',
        params: { url: $scope.item.path }
      };

      $scope.flags.http.fetch_video_info = true;

      http.get(route).then(
        function(response) {
          $scope.item.information     = response.data;

          if ($scope.item.information.title && !$scope.item.title) {
            $scope.item.title = $scope.item.information.title;
          }

          $scope.flags.http.fetch_video_info = false;

          $scope.item.type = $scope.item.information.service;

          $timeout(function() {
            angular.element('.tags-input-buttons .btn-info').triggerHandler('click');
          }, 250);
        },
        function(response) {
          messenger.post(response.data.message);

          $scope.flags.http.fetch_video_info = false;
        }
      );
    };

    /**
     * @function getFrontendUrl
     * @memberOf VideoCtrl
     *
     * @description
     *   Generates the public URL basing on the item.
     *
     * @param {String} item  The item to generate route for.
     *
     * @return {String} The URL for the content.
     */
    $scope.getFrontendUrl = function(item) {
      if (!$scope.selectedCategory) {
        return '';
      }

      return $scope.getL10nUrl(
        routing.generate($scope.routes.public, {
          id: item.pk_content,
          created: $window.moment(item.created).format('YYYYMMDDHHmmss'),
          slug: item.slug,
          category_slug: $scope.selectedCategory.name
        })
      );
    };

    // Update thumbnail when is updated
    $scope.$watch('cover', function(nv) {
      $scope.item.related_contents = [];

      if (!nv) {
        return;
      }

      $scope.item.related_contents.push({
        target_id: nv.pk_content,
        type: 'featured_frontpage',
        content_type_name: 'photo',
        caption: null,
        position: 0
      });
    }, true);

    // Mark preview URLs as trusted on change
    $scope.$watch('item.information.source', function(nv) {
      for (var type in nv) {
        $scope.preview[type] = $scope.trustSrc(nv[type]);
      }
    }, true);
  }
]);
