(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  NewsstandCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $timeout
     * @requires $window
     * @requires routing
     */
    .controller('NewsstandCtrl', [
      '$controller', '$scope', '$timeout', '$window', 'routing', 'translator',
      function($controller, $scope, $timeout, $window, routing, translator) {
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf NewsstandCtrl
         *
         * @description
         *  Flag to enabled or disable drafts.
         *
         * @type {Boolean}
         */
        $scope.draftEnabled = true;

        /**
         * @memberOf NewsstandCtrl
         *
         * @description
         *  The draft key.
         *
         * @type {String}
         */
        $scope.draftKey = 'kiosko-draft';

        /**
         * @memberOf NewsstandCtrl
         *
         * @description
         *  The timeout function for draft.
         *
         * @type {Function}
         */
        $scope.dtm = null;

        /**
         * @memberOf NewsstandCtrl
         *
         * @description
         *   The item.
         *
         * @type {Object}
         */
        $scope.item = {
          categories: [ null ],
          content_status: 0,
          content_type_name: 'kiosko',
          created: new Date(),
          date: '',
          endtime: null,
          favorite: 0,
          fk_content_type: 14,
          path: null,
          starttime: null,
          tags: [],
          thumbnail: null,
          title: '',
        };

        /**
         * @memberOf CoverCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          createItem: 'api_v1_backend_newsstand_create_item',
          getItem:    'api_v1_backend_newsstand_get_item',
          list:       'backend_newsstands_list',
          public:     'frontend_newsstand_show',
          redirect:   'backend_newsstand_show',
          saveItem:   'api_v1_backend_newsstand_save_item',
          updateItem: 'api_v1_backend_newsstand_update_item'
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.localize($scope.data.item, 'item', true);

          if ($scope.item.thumbnail) {
            $scope.preview = $scope.data.extra.paths.newsstand + '/' +
              $scope.item.thumbnail;
          }

          $scope.checkDraft();
          translator.init($scope);
        };

        /**
         * @function convertToFile
         * @memberOf NewsstandCtrl
         *
         * @description
         *   Convert an image as a base64 string to a File.
         *
         * @param {String} url The image as base64 string.
         *
         * @return {File} The image as a File object.
         */
        $scope.convertToFile = function(url) {
          var blobBin = atob(url.split(',')[1]);
          var data    = [];

          for (var i = 0; i < blobBin.length; i++) {
            data.push(blobBin.charCodeAt(i));
          }

          return new File(
            [ new Blob([ new Uint8Array(data) ], { type: 'image/jpg' }) ],
            'image.jpg'
          );
        };

        /**
         * @function generateThumbnail
         * @memberOf NewsstandCtrl
         *
         * @description
         *   Generates a thumbnail from a PDF file and assigns it to the scope.
         *
         * @param {File} file The PDF file.
         */
        $scope.generateThumbnail = function(file) {
          $scope.flags.generate.preview = true;

          var reader = new FileReader();

          reader.onload = function() {
            $window.pdfjsLib.disableWorker = true;

            $window.pdfjsLib.getDocument(new Uint8Array(this.result))
              .then(function(pdf) {
                pdf.getPage(1).then(function(page) {
                  var canvas   = document.createElement('canvas');
                  var viewport = page.getViewport(1.0);
                  var context  = canvas.getContext('2d');

                  // Limit the size of the thumbnail
                  viewport = page.getViewport(650 / viewport.width);

                  canvas.height = viewport.height;
                  canvas.width  = viewport.width;

                  page.render({
                    canvasContext: context,
                    viewport: viewport
                  }).then(function() {
                    $scope.preview        = canvas.toDataURL('image/jpeg', 0.65);
                    $scope.item.thumbnail = $scope.convertToFile($scope.preview);

                    $timeout(function() {
                      $scope.disableFlags('generate');
                    }, 100);
                  });
                });
              }).catch(function() {
                $scope.disableFlags('generate');
              });
          };

          reader.readAsArrayBuffer(file);
        };

        /**
         * @function getFileName
         * @memberOf NewsstandCtrl
         *
         * @description
         *   Returns the filename for a File or a string.
         *
         * @return {String} The filename.
         */
        $scope.getFileName = function() {
          if (!$scope.item.path) {
            return '';
          }

          if (angular.isObject($scope.item.path)) {
            return $scope.item.path.name;
          }

          return $scope.item.name;
        };

        /**
         * @function getFrontendUrl
         * @memberOf NewsstandCtrl
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

          if (!$('[name=form]')[0].checkValidity() || !$scope.item.path) {
            $('[name=form]')[0].reportValidity();
            return false;
          }

          return true;
        };

        // Generates thumbnail when file changes
        $scope.$watch('item.path', function(nv) {
          if (!nv || !angular.isObject(nv)) {
            return;
          }

          $scope.generateThumbnail(nv);
        });
      }
    ]);
})();
