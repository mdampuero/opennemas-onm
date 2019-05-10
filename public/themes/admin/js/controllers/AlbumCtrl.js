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

      if (data.extra.photos && data.extra.photos[$scope.item.cover_id]) {
        $scope.cover = data.extra.photos[$scope.item.cover_id];
      }
    };

    /**
     * @function localizePhotos
     * @memberOf RestInnerCtrl
     *
     * @description
     *   Localizes the photos array and creates linkers
     *
     * @param {Object} data The data in the response.
     */
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
     * @inheritdoc
     */
    $scope.validate = function() {
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

      return true;
    };

    /**
     * Show modal warning for album missing photos
     */
    $scope.validatePhotosAndCover = function() {
      if ($scope.item.photos && $scope.item.cover_id) {
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

    // Update the cover in the item when cover changes
    $scope.$watch('cover', function(nv) {
      $scope.item.cover_id = null;

      if (nv) {
        $scope.item.cover_id = nv.pk_content;
      }
    }, true);

    // Update the ids and footers when photos change
    $scope.$watch('photos', function(nv, ov) {
      if (!nv || nv === ov) {
        return;
      }

      for (var i = 0; i < nv.length; i++) {
        $scope.item.photos.push({
          position: $scope.data.item.photos.length,
          description: nv[i].description,
          pk_photo: nv[i].pk_photo
        });

        if (!$scope.data.extra.photos[nv[i].pk_photo]) {
          $scope.data.extra.photos[nv[i].pk_photo] = nv[i];
        }
      }

      $scope.photos = [];
    }, true);
  }
]);
