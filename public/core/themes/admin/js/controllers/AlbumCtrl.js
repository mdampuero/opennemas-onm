(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  AlbumCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $timeout
     * @requires $uibModal
     * @requires $window
     * @requires related
     * @requires routing
     */
    .controller('AlbumCtrl', [
      '$controller', '$scope', '$timeout', '$uibModal', '$window', 'related', 'routing',
      function($controller, $scope, $timeout, $uibModal, $window, related, routing) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf AlbumCtrl
         *
         * @description
         *  Flag to enabled or disable drafts.
         *
         * @type {Boolean}
         */
        $scope.draftEnabled = true;

        /**
         * @memberOf AlbumCtrl
         *
         * @description
         *  The draft key.
         *
         * @type {String}
         */
        $scope.draftKey = 'album-draft';

        /**
         * @memberOf AlbumCtrl
         *
         * @description
         *  The timeout function for draft.
         *
         * @type {Function}
         */
        $scope.dtm = null;

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
         *  The related contents service
         *
         * @type {Object}
         */
        $scope.related = related;

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
         * @memberOf AlbumCtrl
         *
         * @description
         *  The options for ui-tree directive.
         *
         * @type {Object}
         */
        $scope.treeOptions = {
          /**
           * Sorts the list of original items when the list of localized items
           * is re-ordered.
           *
           * @param {Object} e The event object.
           */
          dropped: function(e) {
            var model  = e.dest.nodesScope.$parent.$parent.data.photos;
            var source = e.source.index;
            var target = e.dest.index;

            var item = model[e.source.index];

            // Remove item
            model.splice(source, 1);

            // Add item
            model.splice(target, 0, item);
          }
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.localize($scope.data.item, 'item', true, [ 'related_contents' ]);

          // Use default value for new items
          if (!$scope.data.item.pk_content) {
            $scope.data.item.with_comment =
              $scope.data.extra.comments_enabled ? 1 : 0;
          }

          $scope.checkDraft();
          related.init($scope);
          related.watch();
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
                return { selected: $scope.photos.length };
              },
              success: function() {
                return function() {
                  return $timeout(function() {
                    $scope.photos      = [];
                    $scope.data.photos = [];

                    // Fake response for ModalCtrl
                    return { response: {}, headers: [], status: 200 };
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
         * @inheritdoc
         */
        $scope.validate = function() {
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
      }
    ]);
})();
