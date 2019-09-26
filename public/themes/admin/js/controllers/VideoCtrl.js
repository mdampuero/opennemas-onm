/**
 * Handle actions for video inner form.
 */
angular.module('BackendApp.controllers').controller('VideoCtrl', [
  '$controller', '$scope', '$timeout', 'http', 'routing', 'messenger',
  function($controller, $scope, $timeout, http, routing, messenger) {
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
      video_url: '',
      information: {}
    };

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
      redirect:   'backend_video_show',
      saveItem:   'api_v1_backend_video_save_item',
      updateItem: 'api_v1_backend_video_update_item'
    };

    /**
     * @inheritdoc
     */
    $scope.buildScope = function() {
      switch ($scope.data.item.author_name) {
        case 'script':
          $scope.setType('script');
          break;

        case 'external':
          $scope.setType('external');

          var info = $scope.data.item.information.source;

          $scope.data.item.type = info.flv ? 'flv' : 'html5';
          break;

        default:
          if ($scope.data.item.video_url) {
            $scope.setType('web-source');
          }
          break;
      }

      $scope.localize($scope.data.item, 'item', true);

      // Check if item is new (created) or existing for use default value or not
      if ($scope.data.item.title.length === 0) {
        $scope.item.with_comment = $scope.data.extra.comments_enabled ? 1 : 0;
      }

      // Assign the cover image
      var cover = $scope.data.extra.related_contents.filter(function(e) {
        return e && e.pk_photo === $scope.item.information.thumbnail;
      }).shift();

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
        $scope.item.author_name = type;
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
        params: { url: $scope.item.video_url }
      };

      $scope.flags.http.fetch_video_info = true;

      http.get(route).then(
        function(response) {
          $scope.item.information     = response.data;
          $scope.item.informationJson = JSON.stringify($scope.information);

          if ($scope.item.information.title && !$scope.item.title) {
            $scope.item.title = $scope.item.information.title;
          }

          $scope.flags.http.fetch_video_info = false;

          $scope.item.author_name = $scope.item.information.service;

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
     * Returns the frontend url for the content given its object
     *
     * @param  {String} item  The object item to generate the url from.
     * @return {String}
     */
    $scope.getFrontendUrl = function(item) {
      var date = item.date;

      var formattedDate = moment(date).format('YYYYMMDDHHmmss');

      return $scope.getL10nUrl(
        routing.generate('frontend_video_show', {
          id: item.pk_content,
          created: formattedDate,
          slug: item.slug,
          category_name: item.category_name
        })
      );
    };

    /**
     * Updates scope when cover changes.
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('cover', function(nv, ov) {
      if (!angular.isObject($scope.item.information)) {
        $scope.item.information = {};
      }

      if (angular.isObject(nv)) {
        $scope.item.information['thumbnail'] = nv.pk_photo;
      } else {
        $scope.item.information.thumbnail = {};
      }
    }, true);
  }
]);
