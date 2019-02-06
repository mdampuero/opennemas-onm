/**
 * Handle actions for poll inner form.
 */
angular.module('BackendApp.controllers').controller('VideoCtrl', [
  '$controller', '$sce', '$rootScope', '$scope', '$timeout', 'http',
  function($controller, $sce, $rootScope, $scope, $timeout, http) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * @memberOf VideoCtrl
     *
     * @description
     *  The list of external video properties.
     *
     * @type {Array}
     */
    $scope.information = [];

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
      $scope.locale = locale;
      $scope.tags   = tags;

      if (video.information) {
        $scope.information = video.information;

        if ($scope.information.embedHTML) {
          $scope.information.embedHTML =
            $sce.trustAsHtml($scope.information.embedHTML);
        }
      }

      if (!$scope.title) {
        $scope.loading_data = false;
      }
    };

    /**
     * @function generateTagsFrom
     * @memberOf InnerCtrl
     *
     * @description
     *   Returns a string to use when clicking on "Generate" button for
     *   tags component.
     *
     * @return {String} The string to generate tags from.
     */
    $scope.generateTagsFrom = function() {
      return $('#title').val();
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
          $scope.information = response.data;

          $scope.informationJson = JSON.stringify($scope.information);

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
