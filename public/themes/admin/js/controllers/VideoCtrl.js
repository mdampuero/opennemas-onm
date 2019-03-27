/**
 * Handle actions for poll inner form.
 */
angular.module('BackendApp.controllers').controller('VideoCtrl', [
  '$controller', '$sce', '$rootScope', '$scope', '$timeout', 'http',
  function($controller, $sce, $rootScope, $scope, $timeout, http) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

    /**
     * @memberOf OpinionCtrl
     *
     * @description
     *  The cover object.
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
      with_comments: 0,
      categories: [],
      related_contents: [],
      tags: [],
      external_link: '',
    };

    /**
     * @memberOf OpinionCtrl
     *
     * @description
     *  Whether to refresh the item after a successful update.
     *
     * @type {Boolean}
     */
    $scope.refreshOnUpdate = true;

    /**
     * @memberOf OpinionCtrl
     *
     * @description
     *  The list of routes for the controller.
     *
     * @type {Object}
     */
    $scope.routes = {
      create:   'api_v1_backend_video_create',
      redirect: 'backend_video_show',
      save:     'api_v1_backend_video_save',
      show:     'api_v1_backend_video_show',
      update:   'api_v1_backend_video_update'
    };

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
     * @function parseItem
     * @memberOf RestInnerCtrl
     *
     * @description
     *   Parses the response and adds information to the scope.
     *
     * @param {Object} data The data in the response.
     */
    $scope.parseItem = function(data) {
      if (data.item) {
        $scope.data.item      = angular.extend($scope.item, data.item);
        $scope.data.item.tags = $scope.item.tags.map(function(id) {
          return data.extra.tags[id];
        });
      }

      $scope.type = $scope.data.item.author_name;

      $scope.configure(data.extra);
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
        name:   'admin_videos_get_info',
        params: { url: $scope.video_url }
      };

      $scope.loading_data = true;

      http.get(route).then(
        function(response) {
          $scope.information     = response.data;
          $scope.informationJson = JSON.stringify($scope.information);

          if ($scope.information.title && !$scope.title) {
            $scope.title = $scope.information.title;
          }

          if ($scope.information.embedHTML) {
            $scope.information.embedHTML =
              $sce.trustAsHtml($scope.information.embedHTML);
          }

          $scope.loading_data = false;

          $timeout(function() {
            angular.element('.tags-input-buttons .btn-info').triggerHandler('click');
          }, 250);
        }
      );
    };
  }
]);
