/**
 * Handle actions for video inner form.
 */
angular.module('BackendApp.controllers').controller('VideoCtrl', [
  '$controller', '$scope', '$timeout', '$window', 'http', 'messenger', 'related', 'routing', 'translator',
  function($controller, $scope, $timeout, $window, http, messenger, related, routing, translator) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

    /**
     * @inheritdoc
     */
    $scope.draftEnabled = true;

    /**
     * @inheritdoc
     */
    $scope.draftKey = 'video-draft';

    /**
     * @inheritdoc
     */
    $scope.dtm = null;

    /**
     * @inheritdoc
     */
    $scope.incomplete = true;

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
     *  The default item object.
     *
     * @type {Object}
     */
    $scope.defaultItem = {
      body: '',
      content_type_name: 'video',
      fk_content_type: 9,
      content_status: 0,
      description: '',
      favorite: 0,
      frontpage: 0,
      created: null,
      starttime: null,
      endtime: null,
      thumbnail: null,
      title: '',
      type: 'web-source',
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
     *  The item object.
     *
     * @type {Object}
     */
    $scope.item = Object.assign({}, $scope.defaultItem);

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
     *  The related service.
     *
     * @type {Object}
     */
    $scope.related = related;

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
      $scope.localize($scope.data.item, 'item', true);

      // Check if item is new (created) or existing for use default value or not
      if (!$scope.data.item.pk_content) {
        $scope.item.with_comment = $scope.data.extra.comments_enabled ? 1 : 0;
      }

      if ($scope.draftKey !== null && $scope.data.item.pk_content) {
        $scope.draftKey = 'video-' + $scope.data.item.pk_content + '-draft';
      }

      $scope.checkDraft();
      related.init($scope);
      related.watch();
      translator.init($scope);
    };

    /**
     * @function selectType
     * @memberOf VideoCtrl
     *
     * @description
     *  Resets the video item when the video type changes.
     */
    $scope.selectType = function(type) {
      if (!type || $scope.item.type === type) {
        return;
      }

      if (type === 'web-source' && ![ 'external', 'script' ].includes($scope.item.type)) {
        return;
      }

      $scope.defaultItem.type = type;
      $scope.item             = angular.copy($scope.defaultItem);
      $scope.data.item        = angular.copy($scope.defaultItem);

      $scope.featuredFrontpage = null;
      $scope.featuredInner     = null;

      $scope.localize($scope.data.item, 'item', true);
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
          $scope.item.information = response.data;

          if ($scope.item.information.title && !$scope.item.title) {
            $scope.item.title = $scope.item.information.title;
          }

          $scope.flags.http.fetch_video_info = false;
          $scope.flags.generate.slug         = true;

          $scope.item.type = $scope.item.information.service;
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

    // Mark preview URLs as trusted on change
    $scope.$watch('item.information.source', function(nv) {
      for (var type in nv) {
        $scope.preview[type] = $scope.trustSrc(nv[type]);
      }
    }, true);
  }
]);
