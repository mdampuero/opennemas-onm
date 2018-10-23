(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  CoverCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $uibModal
     * @requires $window
     * @requires cleaner
     * @requires http
     * @requires messenger
     *
     * @description
     *   Check billing information when saving user.
     */
    .controller('NewsstandCtrl', [
      '$controller', '$scope', 'oqlEncoder', 'oqlDecoder', 'messenger', 'cleaner', '$timeout',
      function($controller, $scope, oqlEncoder, oqlDecoder, messenger, cleaner, $timeout) {
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf CoverCtrl
         *
         * @description
         *  The cover object.
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
          tag_ids: [],
          tags: [],
          thumbnail: null,
          title: '',
          type: 0,
        };

        $scope.files = [];

        /**
         * @memberOf CoverCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          create:   'api_v1_backend_newsstand_create',
          redirect: 'backend_newsstand_show',
          save:     'api_v1_backend_newsstand_save',
          show:     'api_v1_backend_newsstand_show',
          update:   'api_v1_backend_newsstand_update'
        };

        /**
         * @function convertBase64ImageToFile
         * @memberOf CoverCtrl
         *
         * @description
         *  Method to method to convert a base64 encoded image to a File object
         */
        var convertBase64ImageToFile = function(dataUrl) {
          var blobBin = atob(dataUrl.split(',')[1]);
          var array = [];

          for (var i = 0; i < blobBin.length; i++) {
            array.push(blobBin.charCodeAt(i));
          }

          return new File(
            [ new Blob([ new Uint8Array(array) ], { type: 'image/jpg' }) ],
            'image.jpg'
          );
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
          if (!data.item) {
            return;
          }

          data.item.type = Number(data.item.type);
          if (data.item.thumb_url.length > 0) {
            data.item.thumbnail_url = data.extra.KIOSKO_IMG_URL + data.item.path + '/' + data.item.thumb_url;
          }

          $scope.item = angular.extend($scope.item, data.item);
        };

        /**
         * @function getTagsAutoSuggestedFields
         * @memberOf CoverCtrl
         *
         * @description
         *  Method to retrieve text to calculate tags from
         */
        $scope.getTagsAutoSuggestedFields = function() {
          return $scope.title;
        };

        /**
         * @function unsetCover
         * @memberOf CoverCtrl
         *
         * @description
         *  Method to unset the cover information
         */
        $scope.unsetCover = function() {
          $scope.item.name = null;
          $scope.item.cover = null;
          $scope.item.thumbnail = null;
          $scope.item.thumbnail_url = null;
          $scope.item.thumb_url = null;
        };

        /**
         * @function generateThumbnailFromPDF
         * @memberOf CoverCtrl
         *
         * @description
         *  Method to generate a thumbnail from a pdf
         */
        $scope.generateThumbnailFromPDF = function() {
          var file = document.getElementById('cover-file-input').files[0];

          $scope.item.cover = file;

          if (!file) {
            document.getElementById('thumbnail').src = null;
            $scope.item.thumbnail = null;

            return;
          }

          $scope.thumbnailLoading = true;

          var fileReader = new FileReader();

          fileReader.onload = function() {
            pdfjsLib.disableWorker = true;

            var typedarray = new Uint8Array(this.result);

            pdfjsLib.getDocument(typedarray)
              .then(function(pdf) {
                pdf.getPage(1).then(function(page) {
                  var canvas = document.createElement('canvas');
                  var viewport = page.getViewport(1.0);
                  var context = canvas.getContext('2d');

                  // Limit the size of the thumbnail
                  viewport = page.getViewport(650 / viewport.width);

                  canvas.height = viewport.height;
                  canvas.width = viewport.width;

                  page.render({
                    canvasContext: context,
                    viewport: viewport
                  }).then(function() {
                    var dataUrl = canvas.toDataURL('image/jpeg', 0.65);

                    $scope.item.thumbnail = convertBase64ImageToFile(dataUrl);
                    $scope.item.thumbnail_url = dataUrl;
                    $scope.item.name = null;
                    $scope.$apply();

                    $timeout(function() {
                      $scope.thumbnailLoading = false;
                      $scope.$apply();
                    }, 100);
                  });
                });
              })
              .catch(function() {
                $scope.thumbnailLoading = false;
              });
          };

          fileReader.readAsArrayBuffer(file);
        };
      }
    ]);
})();
