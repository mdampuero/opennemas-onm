/**
 * Handle actions for video inner form.
 */
angular.module('BackendApp.controllers').controller('VideoCtrl', [
  '$controller', '$scope', '$timeout', '$window', 'http', 'messenger', 'related', 'routing', 'translator', '$http', '$interval',
  function($controller, $scope, $timeout, $window, http, messenger, related, routing, translator, $http, $interval) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

    $scope.intervalPromise = null;

    /**
     * @inheritdoc
     */
    $scope.draftEnabled = false;

    /**
     * @inheritdoc
     */
    $scope.contentKey = 'video';

    /**
     * @inheritdoc
     */
    $scope.dtm = null;

    /**
     * @memberOf VideoCtrl
     *
     * @description
     *  The list of external video properties.
     *
     * @type {Array}
     */
    $scope.information = {};

    /**
     * @memberOf VideoCtrl
     *
     * @description
     *  The default item object.
     *
     * @type {Object}
     */
    $scope.defaultItem = {
      body: '',
      content_type_name: 'video',
      fk_content_type: 9,
      content_status: 0,
      description: '',
      favorite: 0,
      frontpage: 0,
      created: null,
      starttime: null,
      endtime: null,
      thumbnail: null,
      title: '',
      type: 'upload',
      with_comment: 0,
      categories: [],
      related_contents: [],
      tags: [],
      external_link: '',
      path: '',
      information: {}
    };

    /**
     * @memberOf VideoCtrl
     *
     * @description
     *  The item object.
     *
     * @type {Object}
     */
    $scope.item = Object.assign({}, $scope.defaultItem);

    /**
     * @memberOf VideoCtrl
     *
     * @description
     *   Object with trusted URLs for preview.
     *
     * @type {Object}
     */
    $scope.preview = {};

    /**
     * @memberOf VideoCtrl
     *
     * @description
     *  The related service.
     *
     * @type {Object}
     */
    $scope.related = related;

    /**
     * @memberOf VideoCtrl
     */
    $scope.isDragOver = false;

    /**
     * @memberOf VideoCtrl
     * @description
     *  Buffer size in MB
     */
    $scope.bufferSize = 10;

    /**
     * @memberOf VideoCtrl
     *
     * @description
     *  The list of routes for the controller.
     *
     * @type {Object}
     */
    $scope.routes = {
      createItem: 'api_v1_backend_video_create_item',
      getItem: 'api_v1_backend_video_get_item',
      list: 'backend_videos_list',
      public: 'frontend_video_show',
      redirect: 'backend_video_show',
      saveItem: 'api_v1_backend_video_save_item',
      updateItem: 'api_v1_backend_video_update_item'
    };

    /**
     * @inheritdoc
     */
    $scope.buildScope = function() {
      if (!$scope.data.extra.storage_module && !$scope.data.item.pk_content) {
        $scope.data.item.type = 'web-source';
      }

      $scope.selectedFile = null;
      $scope.progress = -1;
      $scope.uploadComplete = false;
      $scope.totalSizeMB = 0;
      $scope.uploadedSizeMB = 0;
      $scope.estimatedTimeRemaining = '--';
      $scope.uploadStartTime = 0;
      $scope.fileId = '';
      $scope.localize($scope.data.item, 'item', true);
      $scope.expandFields();

      // Check if item is new (created) or existing for use default value or not
      if (!$scope.data.item.pk_content) {
        $scope.item.with_comment = $scope.data.extra.comments_enabled ? 1 : 0;
      }

      $scope.data.item = $scope.parseData($scope.data.item, false);

      $scope.checkDraft();
      related.init($scope);
      related.watch();
      translator.init($scope);

      if ($scope.data.item.pk_content && !$scope.data.item.path && $scope.data.item.type === 'upload') {
        $scope.process();
        $scope.intervalPromise = $interval($scope.fetchStatus, 2500);
      }
    };

    /**
     * @function fetchStatus
     * @memberOf VideoCtrl
     *
     * @description
     *   Fetches the status of the video item from the backend.
     *   If the status is 'done', it processes the video.
     *   If the step is 'Completed', it stops the interval.
     */
    $scope.fetchStatus = function() {
      var route = {
        name: 'api_v1_backend_video_get_item',
        params: { id: $scope.item.pk_content }
      };

      http.get(route).then(
        function(response) {
          $scope.item.information = response.data.item.information || {};
          if ($scope.item.information.status === 'done') {
            $scope.process();
          }
          if ($scope.item.information.step.label === 'Completed') {
            $scope.item.path = response.data.item.path || '';
            $scope.item.related_contents = [];
            var route = {
              name: 'api_v1_backend_photo_get_item',
              params: { id: $scope.item.information.photo }
            };

            http.get(route).then(function(response) {
              $scope.data.extra.related_contents[response.data.item.pk_content] = response.data.item;

              var relatedItem = {
                caption: response.data.item.title,
                content_type_name: "photo",
                position: 0,
                target_id: response.data.item.pk_content,
                type: "featured_frontpage",
              };

              // Clone
              var relatedItemInner = angular.copy(relatedItem);

              $scope.featuredInner = relatedItemInner;
              relatedItemInner.type = "featured_inner";

              $scope.item.related_contents.push(relatedItem);
              $scope.item.related_contents.push(relatedItemInner);

              // Update item
              route.name   = $scope.routes.updateItem;
              route.params = { id: $scope.getItemId() };
              http.put(route, $scope.item).then(function(response) {
                $scope.disableFlags('http');
                  $window.location.href =
                    routing.generate($scope.routes.redirect, { id: $scope.getItemId() });
                messenger.post(response.data);
              }, $scope.errorCb);
            });
            $interval.cancel($scope.intervalPromise);
          }
        },
        function(response) {
          messenger.post({ message: response.data, type: 'error' });
        }
      );
    };

    /**
     * @function process
     * @memberOf VideoCtrl
     *
     * @description
     * Processes the video item, typically after upload or when the video is ready.
     * It sends a request to the backend to process the video.
     * If the request fails, it shows an error message.
     */
    $scope.process = function() {
      var route = {
        name: 'api_v1_backend_storage_process'
      };

      http.post(route, { pk_content: $scope.item.pk_content })
        .catch(function(response) {
          messenger.post({ message: response.data, type: 'error' });
        });
    };

    /**
     * @function selectType
     * @memberOf VideoCtrl
     *
     * @description
     *  Resets the video item when the video type changes.
     */
    $scope.selectType = function(type) {
      if (!type || $scope.item.type === type) {
        return;
      }

      if (type === 'web-source' && !['external', 'script', 'upload'].includes($scope.item.type)) {
        return;
      }

      $scope.defaultItem.type = type;
      $scope.item = angular.copy($scope.defaultItem);
      $scope.data.item = angular.copy($scope.defaultItem);

      $scope.featuredFrontpage = null;
      $scope.featuredInner = null;

      $scope.localize($scope.data.item, 'item', true);
    };

    /**
     * @function getVideoData
     * @memberOf VideoCtrl
     *
     * @description
     *   Gets the video information from the external service.
     */
    $scope.getVideoData = function() {
      var route = {
        name: 'api_v1_backend_video_get_info',
        params: { url: $scope.item.path }
      };

      $scope.flags.http.fetch_video_info = true;

      http.get(route).then(
        function(response) {
          $scope.item.information = response.data;

          if ($scope.item.information.title && !$scope.item.title) {
            $scope.item.title = $scope.item.information.title;
          }

          $scope.flags.http.fetch_video_info = false;
          $scope.flags.generate.slug = true;

          $scope.item.type = $scope.item.information.service;
        },
        function(response) {
          messenger.post(response.data.message);

          $scope.flags.http.fetch_video_info = false;
        }
      );
    };

    /**
     * @function getFrontendUrl
     * @memberOf VideoCtrl
     *
     * @description
     *   Generates the public URL basing on the item.
     *
     * @param {String} item  The item to generate route for.
     *
     * @return {String} The URL for the content.
     */
    $scope.getFrontendUrl = function(item) {
      if (!$scope.selectedCategory || !item.pk_content) {
        return '';
      }

      return $scope.data.extra.base_url + $scope.getL10nUrl(
        routing.generate($scope.routes.public, {
          id: item.pk_content.toString().padStart(6, '0'),
          created: item.urldatetime || $window.moment(item.created).format('YYYYMMDDHHmmss'),
          slug: item.slug,
          category_slug: $scope.selectedCategory.name
        })
      );
    };

    // Mark preview URLs as trusted on change
    $scope.$watch('item.information.source', function(nv) {
      for (var type in nv) {
        $scope.preview[type] = $scope.trustSrc(nv[type]);
      }
    }, true);

    /**
     * Parses the data and calculates text complexity.
     *
     * @param {Object} data - Object containing the text information.
     * @param {string} data.body - The body of the text to analyze.
     * @param {boolean} preview - Indicates if it's a preview (not used in the function).
     * @returns {Object} - The input object with added `text_complexity` and `word_count` properties.
     */
    $scope.parseData = function(data, preview) {
      var bodyComplexity = $scope.getTextComplexity(data.description);

      data.text_complexity = bodyComplexity.textComplexity;
      data.word_count = bodyComplexity.wordsCount;

      return data;
    };

    /**
     * @function getFileName
     * @memberOf AttachmentCtrl
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

      return $scope.item.path.replace(/.*\/([^/]+)/, '$1');
    };

    // Update path in original item when localized item changes
    $scope.$watch('item.type', function(nv) {
      if (nv === 'upload') {
        $scope.uploading = -1;
      }
    });

    /**
     * @function formatSeconds
     * @memberOf VideoCtrl
     *
     * @description
     *  Formats seconds into a human-readable string in the format "MM:SS min".
     * * @param {number} seconds - The number of seconds to format.
     * * @returns {string} The formatted time string.
     */
    $scope.formatSeconds = function(seconds) {
      if (!isFinite(seconds) || seconds < 0) {
        return '--';
      }
      seconds = Math.round(seconds);
      const minutes = Math.floor(seconds / 60);
      const remainingSeconds = seconds % 60;

      return minutes + ':' + (remainingSeconds < 10 ? '0' : '') + remainingSeconds + ' min';
    };

    /**
     * @function triggerFileInput
     * @memberOf VideoCtrl
     * @description
     *   Triggers the file input click event to open the file selection dialog.
     */
    $scope.triggerFileInput = function() {
      document.getElementById('fileInput').click();
    };

    /**
     * @function onDragOver
     * @memberOf VideoCtrl
     * @description
     *   Handles the drag over event to allow file dropping.
     * @param {Event} event - The drag over event.
     */
    $scope.onDragOver = function(event) {
      event.preventDefault();
      $scope.isDragOver = true;
      $scope.$apply();
    };

    /**
     * @function onDragLeave
     * @memberOf VideoCtrl
     * @description
     *   Handles the drag leave event to reset the drag over state.
     * @param {Event} event - The drag leave event.
     */
    $scope.onDragLeave = function(event) {
      event.preventDefault();
      $scope.isDragOver = false;
      $scope.$apply();
    };

    /**
     * @function onDrop
     * @memberOf VideoCtrl
     * @description
     *   Handles the drop event to process the dropped files.
     * @param {Event} event - The drop event.
     */
    $scope.onDrop = function(event) {
      event.preventDefault();
      $scope.isDragOver = false;

      const files = event.dataTransfer.files;

      if (files.length > 0) {
        $scope.setFile(files);
      }
    };

    /**
     * @function generateFileId
     * @memberOf VideoCtrl
     * @description
     *   Generates a unique file ID based on the current timestamp and a random number.
     * @returns {string} A unique file ID.
     */
    $scope.generateFileId = function() {
       return 'file-' + Date.now() + '-' + Math.floor(Math.random() * 1e9);
    };

    /**
     * @function setFile
     * @memberOf VideoCtrl
     * @description
     *   Sets the selected file and initializes upload parameters.
     * @param {FileList} files - The list of files selected by the user.
     */
    $scope.setFile = function(files) {
      var file = files[0];

      if (!file || file.type && file.type.indexOf('video') !== 0) {
        messenger.post({ message: 'Only video files are allowed', type: 'error' });
        return;
      }

      $scope.selectedFile = file;
      $scope.fileId = $scope.generateFileId();
      $scope.progress = 0;
      $scope.uploading = 0;
      $scope.uploadComplete = false;

      $scope.totalSizeMB = ($scope.selectedFile.size / (1024 * 1024)).toFixed(2);
      $scope.uploadedSizeMB = 0;
      $scope.estimatedTimeRemaining = '--';

      $scope.uploadStartTime = Date.now();

      $scope.$apply();
      $scope.uploadInChunks();
    };

    /**
     * @function uploadInChunks
     * @memberOf VideoCtrl
     * @description
     *   Uploads the selected file in chunks to the server.
     *   It calculates the chunk size, uploads each chunk, and updates the progress.
     *   Once all chunks are uploaded, it finalizes the upload and saves the item.
     */
    $scope.uploadInChunks = function() {
      const file = $scope.selectedFile;
      const chunkSize = $scope.bufferSize * 1024 * 1024;
      const totalChunks = Math.ceil(file.size / chunkSize);
      var currentChunk = 0;
      var uploadedBytes = 0;

      $scope.sendNextChunk = function() {
        const start = currentChunk * chunkSize;
        const end = Math.min(file.size, start + chunkSize);
        const chunk = file.slice(start, end);

        const formData = new FormData();

        formData.append('chunk', chunk);
        formData.append('chunkNumber', currentChunk);
        formData.append('totalChunks', totalChunks);
        formData.append('fileName', file.name);
        formData.append('fileSize', file.size);
        formData.append('fileId', $scope.fileId);
        formData.append('fileType', 'video');

        $http.post(routing.generate('api_v1_backend_storage_upload_chunk'), formData, {
          /* eslint-disable-next-line no-undefined */
          headers: { 'Content-Type': undefined },
          transformRequest: angular.identity
        }).then(function(response) {
          uploadedBytes += end - start;
          $scope.progress = Math.floor(uploadedBytes / file.size * 100);
          $scope.uploadedSizeMB = (uploadedBytes / (1024 * 1024)).toFixed(2);
          const elapsedSeconds = (Date.now() - $scope.uploadStartTime) / 1000;
          const uploadSpeed = uploadedBytes / elapsedSeconds;
          const remainingBytes = file.size - uploadedBytes;
          const estimatedRemaining = remainingBytes / uploadSpeed;

          $scope.estimatedTimeRemaining = $scope.formatSeconds(estimatedRemaining);

          $scope.$applyAsync();

          currentChunk++;

          if (currentChunk < totalChunks) {
            $scope.sendNextChunk();
          } else {
            if (response.data.status === 'done') {
              $scope.flags.http.saving = true;
              $scope.progress = 100;
              $scope.uploading = 1;
              $scope.uploadComplete = true;
              $scope.filePath = response.data.filePath;
              $scope.item.information = response.data;
              $scope.item.title = response.data.fileName;

              if (response.data.step.label === 'Completed') {
                $scope.item.path = response.data.relativePath;
              }

              $scope.getSlug($scope.item.title, function(response) {
                $scope.item.slug = response.data.slug;
                $scope.flags.generate.slug = false;
                $scope.flags.block.slug = true;
                $scope.saveWithoutValidate();
              });
            }
            $scope.$applyAsync();
          }
        }, function(error) {
          messenger.post({ message: error.data && error.data.message ? error.data.message : 'Upload failed', type: 'error' });
          $scope.$applyAsync();
        });
      };

      $scope.sendNextChunk();
    };

    /**
     * @function saveWithoutValidate
     * @memberOf VideoCtrl
     */
    $scope.saveWithoutValidate = function() {
      var route = { name: $scope.routes.saveItem };
      var successCb = function(response) {
        $scope.disableFlags('http');

        if ($scope.routes.redirect && response.status === 201) {
          var id = response.headers().location
            .substring(response.headers().location.lastIndexOf('/') + 1);

          $window.location.href =
            routing.generate($scope.routes.redirect, { id: id });
        }

        if (response.status === 200 && $scope.refreshOnUpdate) {
          $timeout(function() {
            $scope.getItem($scope.getItemId());
          }, 500);
        }

        messenger.post(response.data);
      };

      http.post(route, $scope.item).then(successCb, $scope.errorCb);
    };

    $scope.$on('$destroy', function() {
      $interval.cancel($scope.intervalPromise);
    });

    /**
     * @function copyPath
     * @memberOf VideoCtrl
     *
     * @description
     *   Copies the video path to the clipboard.
     *   If the path is empty, it does nothing.
     */
    $scope.copyPath = function() {
      if (!$scope.item.path) {
        return;
      }

      var tempInput = document.createElement('input');

      tempInput.style.position = 'absolute';
      tempInput.style.left = '-9999px';
      tempInput.value = $scope.item.path;
      document.body.appendChild(tempInput);
      tempInput.select();
      document.execCommand('copy');
      document.body.removeChild(tempInput);
    };
  }
]);
