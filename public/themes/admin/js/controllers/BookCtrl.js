/**
 * Handle actions for poll inner form.
 */
angular.module('BackendApp.controllers').controller('BookCtrl', [
  '$controller', '$rootScope', '$scope',
  function($controller, $rootScope, $scope) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * @function init
     * @memberOf BookCtrl
     *
     * @description
     * Method to init the book controller
     *
     * @param {object} book     Book to edit
     * @param {String} locale   Locale for the book
     * @param {Array}  tags     Array with all the tags needed for the book
     */
    $scope.init = function(book, locale, tags) {
      $scope.tag_ids = book !== null ? book.tag_ids : [];
      $scope.locale  = locale;
      $scope.tags    = tags;
      $scope.watchTagIds('title');
    };

    /**
     * @function getTagsAutoSuggestedFields
     * @memberOf BookCtrl
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
     *
     * @param array nv The new values.
     * @param array ov The old values.
     */
    $scope.$watch('book_cover', function(nv, ov) {
      $scope.book_cover_id = null;

      if ($scope.book_cover) {
        $scope.book_cover_id = $scope.book_cover.id;
      }
    }, true);
  }
]);
