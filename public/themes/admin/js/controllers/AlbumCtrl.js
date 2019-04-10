/**
 * Handle actions for album inner form.
 */
angular.module('BackendApp.controllers').controller('AlbumCtrl', [
  '$controller', '$rootScope', '$scope', '$uibModal', 'messenger', 'routing', 'localizer', 'linker',
  function($controller, $rootScope, $scope, $uibModal, messenger, routing, localizer, linker) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

    /**
     * @memberOf VideoCtrl
     *
     * @description
     *  The item object.
     *
     * @type {Object}
     */
    $scope.item = {
      body: '',
      content_type_name: 'album',
      fk_content_type: 7,
      content_status: 0,
      description: '',
      favorite: 0,
      frontpage: 0,
      created: new Date(),
      starttime: null,
      endtime: null,
      thumbnail: null,
      title: '',
      type: 0,
      with_comments: 0,
      categories: [],
      related_contents: [],
      tags: [],
      external_link: '',
      agency: '',
      cover: null,
      cover_image: null,
      photos: [],
    };

    /**
     * @memberOf VideoCtrl
     *
     * @description
     *  The list of routes for the controller.
     *
     * @type {Object}
     */
    $scope.routes = {
      create:   'api_v1_backend_album_create',
      redirect: 'backend_album_show',
      save:     'api_v1_backend_album_save',
      show:     'api_v1_backend_album_show',
      update:   'api_v1_backend_album_update'
    };

    $scope.expanded = {
      cover_image: true,
    };

    /**
     * @function parseItem
     * @memberOf RestInnerCtrl
     *
     * @description
     *   Parses the response and adds information to the scope.
     *
     * @param {Object} data The data in the response.
     */
    $scope.parseItem = function(data) {
      if (data.item) {
        $scope.data.item = angular.extend($scope.item, data.item);
      }

      $scope.configure(data.extra);
      $scope.localize($scope.data.item, 'item', true, [ 'photos' ]);
      $scope.localizePhotos($scope.data.item.photos, 'photos', true);

      // Assign the cover image
      var cover = data.extra.photos.filter(function(el) {
        return el && el.pk_photo === $scope.item.cover_id;
      }).shift();

      if (cover) {
        $scope.cover_image = cover;
      }
    };

    $scope.localizePhotos = function(items, key, clean) {
      var lz = localizer.get($scope.config.locale);

      // Localize items
      $scope.item[key] = lz.localize(items, [ 'description' ], $scope.config.locale);

      // Initialize linker
      if (!$scope.config.linkers[key]) {
        $scope.config.linkers[key] = linker.get([ 'description' ],
          $scope.config.locale.default, $scope, clean);
      }

      // Link original and localized items
      $scope.config.linkers[key].setKey($scope.config.locale.selected);
      $scope.config.linkers[key].link(items, $scope.item[key]);
    };

    /**
     * @function getFrontendUrl
     * @memberOf AlbumCtrl
     *
     * @description
     * Returns the frontend url for the content given its object
     *
     * @param  {String} item  The object item to generate the url from.
     * @return {String}
     */
    $scope.getFrontendUrl = function(item) {
      var date = item.date;

      var formattedDate = window.moment(date).format('YYYYMMDDHHmmss');

      return $scope.getL10nUrl(
        routing.generate('frontend_album_show', {
          id: item.pk_content,
          created: formattedDate,
          slug: item.slug,
          category_name: item.category_name
        })
      );
    };

    /**
     * @function getPhotoData
     * @memberOf AlbumCtrl
     *
     * @description
     *   Returns the info of a photo from the extra parameters given the photo id.
     */
    $scope.getPhotoData = function(photoId) {
      if (!photoId || !$scope.data.extra || $scope.data.extra.photos.length == 0) {
        return {};
      }

      var photos = $scope.data.extra.photos.filter(function(el) {
        return el.pk_photo === photoId;
      });

      return photos.shift();
    };

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
    $scope.submit = function() {
      if (!$scope.validatePhotosAndCover()) {
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
          $('[name=tags]').val(JSON.stringify(ids));
          $('[name=form]').submit();
        }
      });

      $rootScope.submit();

      return true;
    };

    /**
     * Show modal warning for album missing photos
     */
    $scope.validatePhotosAndCover = function() {
      if ($scope.photos && $scope.item.cover) {
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
    $scope.$watch('item.cover_image', function(nv, ov) {
      if (nv === ov) {
        return false;
      }

      $scope.item.cover = nv.pk_content;
    }, true);

    /**
     * Updates the ids and footers when photos change.
     *
     * @param Object nv The new values.
     * @param Object ov The old values.
     */
    $scope.$watch('item.photos', function(nv, ov) {
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
