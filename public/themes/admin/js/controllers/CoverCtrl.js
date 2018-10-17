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
    .controller('CoverCtrl', [
      '$controller', '$scope', 'oqlEncoder', 'oqlDecoder', 'messenger', 'cleaner',
      function($controller, $scope, oqlEncoder, oqlDecoder, messenger, cleaner) {
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
          content_status: 0,
          favorite: '',
          category: null,
          tags: [],

          title: '',
          date: '',
          price: 0,
          type: 0,
          file: '',
          thumbnail: '',
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
          create:   'api_v1_backend_cover_create',
          redirect: 'backend_cover_show',
          save:     'api_v1_backend_cover_save',
          show:     'api_v1_backend_cover_show',
          update:   'api_v1_backend_cover_update'
        };

        /**
         * @function getData
         * @memberOf SubscriberCtrl
         *
         * @description
         *   Returns the data to send when saving/updating an item.
         */
        $scope.getData = function() {
          // Do not use angular.copy as it doesnt copy some keys in the object
          var eltoClean = angular.extend({}, $scope.item);

          var data = cleaner.clean(eltoClean);

          var formData = new FormData();

          angular.forEach(data, function(value, key) {
            formData.append(key, value);
          });

          angular.forEach($scope.files, function(value, key) {
            formData.append('file' + key, value);
          });

          return formData;
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

          data.item.content_status = Number(data.item.content_status);
          data.item.favorite       = Number(data.item.favorite);
          data.item.type           = Number(data.item.type);

          $scope.item = angular.extend($scope.item, data.item);
        };

        /**
         * @function getTagsAutoSuggestedFields
         * @memberOf CoverCtrl
         *
         * @description
         *  Method to method to retrieve th title for the autosuggested words
         */
        $scope.getTagsAutoSuggestedFields = function() {
          return $scope.title;
        };

        $scope.generateThumbnailFromPDF = function() {
          var file = document.getElementById('cover-file-input').files[0];

          $scope.files.push(file);

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

                  viewport = page.getViewport(300 / viewport.width);

                  canvas.height = viewport.height;
                  canvas.width = viewport.width;

                  page.render({
                    canvasContext: context,
                    viewport: viewport
                  }).then(function() {
                    var info = canvas.toDataURL();

                    $scope.thumbnailLoading = false;
                    $scope.item.thumbnail = info;
                    document.getElementById('thumbnail').src = info;
                  }).catch(function() {
                    $scope.thumbnailLoading = false;
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
