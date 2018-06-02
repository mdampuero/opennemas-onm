/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('SpecialCtrl', [
  '$controller', '$rootScope', '$scope',
  function($controller, $rootScope, $scope) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * @function init
     * @memberOf SpecialCtrl
     *
     * @description
     * Method to init the special controller
     *
     * @param {object} special  Special to edit
     * @param {String} locale   Locale for the special
     * @param {Array}  tags     Array with all the tags needed for the special
     */
    $scope.init = function(special, locale, tags) {
      $scope.tag_ids = special !== null ? special.tag_ids : [];
      $scope.locale  = locale;
      $scope.tags    = tags;
      $scope.watchTagIds('title');
    };

    /**
     * Parse the photos from template and initialize the scope properly
     *
     * @param Object photos The album photos.
     */
    $scope.parsePhotos = function(photos) {
      $scope.footers = [];
      $scope.ids     = [];
      $scope.photos  = [];

      for (var i = 0; i < photos.length; i++) {
        $scope.footers.push(photos[i].description);
        $scope.ids.push(photos[i].id);
        $scope.photos.push(photos[i].photo);
      }
    };

    /**
     * @function getTagsAutoSuggestedFields
     * @memberOf SpecialCtrl
     *
     * @description
     *   Method to method to retrieve th title for the autosuggested words
     *
     */
    $scope.getTagsAutoSuggestedFields = function() {
      return $scope.title;
    };

    /**
     * Updates scope when photo1 changes.
     */
    $scope.$watch('photo1', function() {
      $scope.img1 = null;

      if ($scope.photo1) {
        $scope.img1 = $scope.photo1.id;
      }
    }, true);

    /**
     * Updates scope when relatedInFrontpage changes.
     *
     * @param array nv The new values.
     */
    $scope.$watch('contentsLeft', function(nv) {
      $scope.relatedLeft = [];
      var items          = [];

      if (nv instanceof Array) {
        for (var i = 0; i < nv.length; i++) {
          items.push({
            id: nv[i].id, position: i, content_type: nv[i].content_type_name
          });
        }
      }

      $scope.relatedLeft = angular.toJson(items);
    }, true);

    /**
     * Updates scope when relatedInInner changes.
     *
     * @param array nv The new values.
     */
    $scope.$watch('contentsRight', function(nv) {
      $scope.relatedRight = [];
      var items           = [];

      if (nv instanceof Array) {
        for (var i = 0; i < nv.length; i++) {
          items.push({
            id: nv[i].id, position: i, content_type: nv[i].content_type_name
          });
        }
      }

      $scope.relatedRight = angular.toJson(items);
    }, true);
  }
]);
