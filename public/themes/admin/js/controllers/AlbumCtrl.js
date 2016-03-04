angular.module('BackendApp.controllers')
  /**
   * Handle actions for article inner.
  */
  .controller('AlbumCtrl', ['$controller', '$rootScope', '$scope', '$uibModal',
  function($controller, $rootScope, $scope, $uibModal) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * Parse the photos from template and initialize the scope properly
     *
     * @param Object photos The album photos.
     */
    $scope.parsePhotos = function(photos) {
      $scope.photos  = [];

      for (var i = 0; i < photos.length; i++) {
        photos[i].photo.footer = photos[i].description;
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

      for (var i = 0; i < nv.length; i++) {
        if (nv[i].footer === undefined) {
          nv[i].footer = nv[i].description;
        }
      }
    }, true);

    /**
     * Show modal warning for album missing photos
     */
    $scope.validatePhotosAndCover = function($event) {
      if (!$scope.photos || !$scope.cover) {
        $event.preventDefault();
        var modal = $uibModal.open({
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
