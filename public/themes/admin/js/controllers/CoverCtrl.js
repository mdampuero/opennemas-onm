angular.module('BackendApp.controllers')

  /**
   * @ngdoc controller
   * @name  CoverCtrl
   *
   * @description
   *   Handles actions for cover inner
   *
   * @requires $controller
   * @requires $rootScope
   * @requires $scope
   */
  .controller('CoverCtrl', [
    '$controller', '$rootScope', '$scope',
    function($controller, $rootScope, $scope) {
      'use strict';

      // Initialize the super class and extend it.
      $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

      /**
       * @function init
       * @memberOf CoverCtrl
       * Method to init the cover controller
       *
       * @param {object} cover  Cover to edit
       * @param {String} locale Locale for the cover
       * @param {Array}  tags   Array with all the tags needed for the cover
       */
      $scope.init = function(cover, locale, tags) {
        $scope.tag_ids = cover !== null ? cover.tag_ids : [];
        $scope.locale  = locale;
        $scope.tags    = tags;
        $scope.watchTagIds('title');
      };

      /**
       * @function getTagsAutoSuggestedFields
       * @memberOf CoverCtrl
       *
       * @description
       *  Method to method to retrieve th title for the autosuggested words
       */
      $scope.getTagsAutoSuggestedFields = function() {
        return $scope.title;
      };
    }
  ]);
