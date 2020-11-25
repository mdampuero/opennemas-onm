(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  AlbumCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $uibModal
     * @requires $window
     * @requires linker
     * @requires localizer
     * @requires messenger
     * @requires routing
     */
    .controller('AlbumCtrl', [
      '$controller', '$scope', '$timeout', '$uibModal', '$window', 'linker', 'localizer', 'messenger', 'routing',
      function($controller, $scope, $timeout, $uibModal, $window, linker, localizer, messenger, routing) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf AlbumCtrl
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
          with_comment: 0,
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
         * @memberOf AlbumCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          createItem: 'api_v1_backend_album_create_item',
          getItem:    'api_v1_backend_album_get_item',
          list:       'backend_albums_list',
          public:     'frontend_album_show',
          redirect:   'backend_album_show',
          saveItem:   'api_v1_backend_album_save_item',
          updateItem: 'api_v1_backend_album_update_item'
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.localize($scope.data.item, 'item', true, [ 'photos' ]);

          // Check if item is new (created) or existing for use default value or not
          if (!$scope.data.item.pk_content) {
            $scope.item.with_comment = $scope.data.extra.comments_enabled ? 1 : 0;
          }

          // Remove unexisting photos from response data
          $scope.data.item.photos = $scope.data.item.photos.filter(function(e) {
            return $scope.data.extra.photos[e.pk_photo];
          });

          $scope.item.photos = [];

          for (var i = 0; i < $scope.data.item.photos.length; i++) {
            // Localize photos 1 by 1
            $scope.item.photos.push($scope.localizePhoto(
              $scope.data.item.photos[i], $scope.item.photos.length));
          }

          if ($scope.data.extra.photos &&
              $scope.data.extra.photos[$scope.item.cover_id]) {
            $scope.cover = $scope.data.extra.photos[$scope.item.cover_id];
          }
        };

        /**
         * @function empty
         * @memberOf AlbumCtrl
         *
         * @description
         *   Shows a modal window to confirm if album has to be emptied.
         */
        $scope.empty = function() {
          $uibModal.open({
            templateUrl: 'modal-delete',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return { selected: $scope.item.photos.length };
              },
              success: function() {
                return function(modalWindow) {
                  return $timeout(function() {
                    $scope.item.photos      = [];
                    $scope.data.item.photos = [];

                    modalWindow.close();
                  });
                };
              }
            }
          });
        };

        /**
         * @function getFrontendUrl
         * @memberOf AlbumCtrl
         *
         * @description
         *   Generates the public URL basing on the item.
         *
         * @param {String} item  The item to generate route for.
         *
         * @return {String} The URL for the content.
         */
        $scope.getFrontendUrl = function(item) {
          if (!$scope.selectedCategory) {
            return '';
          }

          return $scope.getL10nUrl(
            routing.generate($scope.routes.public, {
              id: item.pk_content,
              created: $window.moment(item.created).format('YYYYMMDDHHmmss'),
              slug: item.slug,
              category_slug: $scope.selectedCategory.name
            })
          );
        };

        /**
         * @function localizePhoto
         * @memberOf AlbumCtrl
         *
         * @description
         *   Localizes a photo in the array of photos.
         *
         * @param {Object}  original The photo to localize.
         * @param {Integer} index    The index in the array of photos to use as
         *                           linker name.
         */
        $scope.localizePhoto = function(original, index) {
          var localized = localizer.get($scope.config.locale).localize(original,
            [ 'description' ], $scope.config.locale);

          // Initialize linker
          delete $scope.config.linkers[index];
          $scope.config.linkers[index] = linker.get([ 'description' ],
            $scope.config.locale.default, $scope, true);

          // Link original and localized items
          $scope.config.linkers[index].setKey($scope.config.locale.selected);
          $scope.config.linkers[index].link(original, localized);

          return localized;
        };

        /**
         * @inheritdoc
         */
        $scope.validate = function() {
          if (!$scope.validatePhotosAndCover()) {
            return false;
          }

          if ($scope.form && $scope.form.$invalid) {
            $('[name=form]')[0].reportValidity();
            return false;
          }

          if (!$('[name=form]')[0].checkValidity()) {
            $('[name=form]')[0].reportValidity();
            return false;
          }

          return true;
        };

        /**
         * Shows a warning in a modal when cover and/or photos are missing.
         */
        $scope.validatePhotosAndCover = function() {
          if ($scope.item.photos && $scope.item.photos.length > 0 &&
              $scope.item.cover_id) {
            return true;
          }

          $uibModal.open({
            templateUrl: 'modal-edit-album-error',
            backdrop: 'static',
            controller: 'ModalCtrl',
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

        // Update photos order when it changes
        $scope.$watch('item.photos', function(nv, ov) {
          if (!nv || nv === ov) {
            return;
          }

          var ids = nv.map(function(e) {
            return e.pk_photo;
          });

          $scope.data.item.photos.sort(function(a, b) {
            return ids.indexOf(a.pk_photo) - ids.indexOf(b.pk_photo);
          });
        }, true);

        // Update the ids and footers when photos change
        $scope.$watch('photos', function(nv, ov) {
          if (!nv || nv === ov) {
            return;
          }

          for (var i = 0; i < nv.length; i++) {
            var photo = {
              position: $scope.data.item.photos.length,
              description: nv[i].description,
              pk_photo: nv[i].pk_content
            };

            $scope.data.item.photos.push(photo);

            // Localize and add new photo to localized item
            $scope.item.photos.push($scope.localizePhoto(photo,
              $scope.item.photos.length));

            if (!$scope.data.extra.photos[nv[i].pk_content]) {
              $scope.data.extra.photos[nv[i].pk_content] = nv[i];
            }
          }

          $scope.photos = [];
        }, true);
      }
    ]);
})();
