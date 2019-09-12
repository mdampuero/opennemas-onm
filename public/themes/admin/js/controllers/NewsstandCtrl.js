(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  CoverCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $timeout
     * @requires routing
     */
    .controller('NewsstandCtrl', [
      '$controller', '$scope', '$timeout', 'routing',
      function($controller, $scope, $timeout, routing) {
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

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
          $scope.item.type = Number($scope.item.type);

          if ($scope.item.thumb_url && $scope.item.thumb_url.length > 0) {
            $scope.item.thumbnail_url = $scope.data.extra.KIOSKO_IMG_URL +
              $scope.item.path + '/' + $scope.item.thumb_url;
          }
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

        /**
         * @function submit
         * @memberOf NewsstandCtrl
         *
         * @description
         *   Saves tags and, then, saves the item.
         */
        $scope.submit = function() {
          if (!$('[name=form]')[0].checkValidity()) {
            $('[name=form]')[0].reportValidity();
            return;
          }

          $scope.flags.http.saving = true;

          $scope.$broadcast('onmTagsInput.save', {
            onError: $scope.errorCb,
            onSuccess: function(ids) {
              $scope.item.tags = ids;
              $scope.save();
            }
          });
        };

        /**
         * @function unsetCover
         * @memberOf CoverCtrl
         *
         * @description
         *  Method to unset the cover information
         */
        $scope.unsetCover = function() {
          $scope.item.name          = null;
          $scope.item.cover         = null;
          $scope.item.thumbnail     = null;
          $scope.item.thumbnail_url = null;
          $scope.item.thumb_url     = null;
        };
      }
    ]);
})();
