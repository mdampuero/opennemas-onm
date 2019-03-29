angular.module('BackendApp.controllers')

  /**
   * Handle actions for article inner.
   */
  .controller('AlbumCtrl', [
    '$controller', '$rootScope', '$scope', '$uibModal', 'messenger',
    function($controller, $rootScope, $scope, $uibModal, messenger) {
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

        $('.btn.btn-primary').attr('disabled', false);
      };

      /**
       * @function submit
       * @memberOf AlbumCtrl
       *
       * @description
       *   Saves tags and, then, submits the form.
       */
      $scope.submit = function(e) {
        e.preventDefault();

        if (!$scope.validatePhotosAndCover(e)) {
          return false;
        }

        if ($scope.form.$invalid) {
          $('[name=form]')[0].reportValidity();
          messenger.post(window.strings.forms.not_valid, 'error');

          return false;
        }

        if (!$('[name=form]')[0].checkValidity()) {
          $('[name=form]')[0].reportValidity();
          return false;
        }

        $scope.$broadcast('onmTagsInput.save', {
          onSuccess: function(ids) {
            $('[name=tag_ids]').val(JSON.stringify(ids));
            $('[name=form]').submit();
          }
        });
      };

      /**
       * Show modal warning for album missing photos
       */
      $scope.validatePhotosAndCover = function() {
        if ($scope.photos && $scope.cover) {
          return true;
        }

        $uibModal.open({
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

        return false;
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
          if (typeof nv[i].footer === 'undefined') {
            nv[i].footer = nv[i].description;
          }
        }
        return null;
      }, true);
    }
  ]);
