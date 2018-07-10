/**
 * Handle actions for poll inner form.
 */
angular.module('BackendApp.controllers').controller('VideoCtrl', [
  '$controller', '$rootScope', '$scope', '$sce', '$timeout', 'http',
  function($controller, $rootScope, $scope, $sce, $timeout, http) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * @function init
     * @memberOf VideoCtrl
     *
     * @description
     * Method to init the video controller
     *
     * @param {object} video    Video to edit
     * @param {String} locale   Locale for the video
     * @param {Array}  tags     Array with all the tags needed for the video
     */
    $scope.init = function(video, locale, tags) {
      $scope.tag_ids          = video !== null ? video.tag_ids : [];
      $scope.locale           = locale;
      $scope.tags             = tags;
      if (!$scope.title) {
        $scope.loading_data     = false;
        $scope.external_content = '';
      } else {
        $scope.watchTagIds('title');
      }
    };

    /**
     * @function getTagsAutoSuggestedFields
     * @memberOf VideoCtrl
     *
     * @description
     *   Method to method to retrieve th title for the autosuggested words
     *
     */
    $scope.getTagsAutoSuggestedFields = function() {
      return $scope.title ? $scope.title : $('#title').val();
    };

    $scope.getVideoData = function() {
      var route = {
        name: 'admin_videos_get_info',
        params: {
          url: $scope.video_url
        }
      };

      $scope.loading_data     = true;
      $scope.external_content = '';

      http.get(route).then(
        function(response) {
          $scope.external_content = $sce.trustAsHtml(response.data);
          $scope.loading_data     = false;
          $timeout(function() {
            $scope.loadAutoSuggestedTags();
          }, 0);
        }
      );
    };
  }
]);
