/**
 * Handle actions for file inner.
 */
angular.module('BackendApp.controllers').controller('FileCtrl', [
  '$controller', '$rootScope', '$scope',
  function($controller, $rootScope, $scope) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * @function init
     * @memberOf FileCtrl
     *
     * @description
     * Method to init the file controller
     *
     * @param {object} file     File to edit
     * @param {String} locale   Locale for the file
     * @param {Array}  tags     Array with all the tags needed for the file
     */
    $scope.init = function(file, locale, tags) {
      $scope.tag_ids = file !== null ? file.tag_ids : [];
      $scope.locale  = locale;
      $scope.tags    = tags;
      $scope.watchTagIds('title');
    };

    /**
     * @function getTagsAutoSuggestedFields
     * @memberOf FileCtrl
     *
     * @description
     *   Method to method to retrieve th title for the autosuggested words
     *
     */
    $scope.getTagsAutoSuggestedFields = function() {
      return $scope.title;
    };
  }
]);
