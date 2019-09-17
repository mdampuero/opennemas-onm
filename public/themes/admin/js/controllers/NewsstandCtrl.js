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
      '$controller', '$scope', '$timeout', '$window', 'routing',
      function($controller, $scope, $timeout, $window, routing) {
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf NewsstandCtrl
         *
         * @description
         *   The item.
         *
         * @type {Object}
         */
        $scope.item = {
          category: null,
          content_status: 0,
          cover: null,
          date: '',
          favorite: 0,
          file: '',
          price: 0,
          tags: [],
          thumbnail: null,
          title: '',
          type: 0,
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
          public:     'frontend_newsstand_show',
          redirect:   'backend_newsstand_show',
          saveItem:   'api_v1_backend_newsstand_save_item',
          updateItem: 'api_v1_backend_newsstand_update_item'
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          if ($scope.item.thumbnail) {
            $scope.preview = $scope.data.extra.paths.newsstand + '/' +
              $scope.item.thumbnail;
          }
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
         * @param  {String} item  The item to generate route for.
         *
         * @return {String} The URL for the content.
         */
        $scope.getFrontendUrl = function(item) {
          var date = item.created;
          var formattedDate = window.moment(date).format('YYYYMMDDHHmmss');

          return $scope.getL10nUrl(
            routing.generate($scope.routes.public, {
              id:            item.pk_content,
              created:       formattedDate,
              category_name: item.category_name
            })
          );
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
