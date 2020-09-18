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
     * @function getContentIds
     * @memberOf specialCtrl
     *
     * @description
     *   Returns the list of ids of all contents added to the special.
     *
     * @return {Array} The list of ids.
     */
    $scope.getContentIds = function() {
      var left = !$scope.contentsLeft ? [] : $scope.contentsLeft.map(function(e) {
        return e.pk_content;
      });

      var right = !$scope.contentsRight ? [] : $scope.contentsRight.map(function(e) {
        return e.pk_content;
      });

      return left.concat(right);
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

    // Updates scope when photo1 changes.
    $scope.$watch('photo1', function() {
      $scope.img1 = null;

      if ($scope.photo1) {
        $scope.img1 = $scope.photo1.pk_content;
      }
    }, true);

    // Update scope when contentsLeft changes
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

    // Update scope when contentsRight changes
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

    // Add contents to left column on content picker insert
    $scope.$watch('tmp.contentsLeft', function(nv) {
      if (!nv) {
        return;
      }

      if (!$scope.contentsLeft) {
        $scope.contentsLeft = [];
      }

      for (var i = 0; i < nv.length; i++) {
        $scope.contentsLeft.push(nv[i]);
      }

      $scope.tmp.contentsLeft = [];
    }, true);

    // Add contents to right column on content picker insert
    $scope.$watch('tmp.contentsRight', function(nv) {
      if (!nv) {
        return;
      }

      if (!$scope.contentsRight) {
        $scope.contentsRight = [];
      }

      for (var i = 0; i < nv.length; i++) {
        $scope.contentsRight.push(nv[i]);
      }

      $scope.tmp.contentsRight = [];
    }, true);
  }
]);
