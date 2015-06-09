angular.module('BackendApp.controllers')
  /**
   * Handle actions for article inner.
  */
  .controller('AlbumCtrl', ['$controller', '$rootScope', '$scope', '$modal',
  function($controller, $rootScope, $scope, $modal) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

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
     * Updates the ids and footers when photos change.
     *
     * @param Object nv The new values.
     * @param Object ov The old values.
     */
    $scope.$watch('photos', function(nv, ov) {
      if (nv === ov) {
        return false;
      }

      if (!$scope.footers) {
        $scope.footers = [];
      }

      $scope.ids = [];

      for (var i = 0; i < nv.length; i++) {
        if (!$scope.footers[i]) {
          $scope.footers.push(nv[i].description);
        }

        $scope.ids.push(nv[i].id);
      }
    }, true);

    /**
     * Show modal warning for album missing photos
     */
    $scope.validatePhotosAndCover = function($event) {
      if (!$scope.photos || !$scope.cover) {
        $event.preventDefault();
        var modal = $modal.open({
          templateUrl: 'modal-edit-album-error',
          backdrop: 'static',
          controller: 'modalCtrl',
          resolve: {
            template: function() {
              return null;
            },
            success: function() {
              return null;
            }
          }
        });
      }
    };
  }
]);
