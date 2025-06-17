/**
 * Handle actions for video inner form.
 */
angular.module('BackendApp.controllers').controller('VideoCtrl', [
  '$controller', '$scope', '$timeout', '$window', 'http', 'messenger', 'related', 'routing', 'translator', '$http',
  function($controller, $scope, $timeout, $window, http, messenger, related, routing, translator, $http) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

    /**
     * @inheritdoc
     */
    $scope.draftEnabled = true;

    /**
     * @inheritdoc
     */
    $scope.draftKey = 'video-draft';

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
      $scope.localize($scope.data.item, 'item', true);
      $scope.expandFields();
      // Check if item is new (created) or existing for use default value or not
      if (!$scope.data.item.pk_content) {
        $scope.item.with_comment = $scope.data.extra.comments_enabled ? 1 : 0;
      }

      $scope.data.item = $scope.parseData($scope.data.item, false);

      if ($scope.draftKey !== null && $scope.data.item.pk_content) {
        $scope.draftKey = 'video-' + $scope.data.item.pk_content + '-draft';
      }

      $scope.checkDraft();
      related.init($scope);
      related.watch();
      translator.init($scope);
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
    $scope.$watch('item.path', function(nv) {
      if ($scope.data && $scope.data.item) {
        $scope.data.item.path = nv;
      }
    });

    $scope.selectedFile = null;
    $scope.progress = -1;
    $scope.uploadComplete = false;
    $scope.uploadError = false;

    // Genera un ID único por archivo
    $scope.generateFileId = function() {
      return 'file-' + Date.now() + '-' + Math.floor(Math.random() * 100000);
    };

    // Se llama al cambiar el input file
    $scope.setFile = function(files) {
      $scope.selectedFile = files[0];
      $scope.fileId = $scope.generateFileId();
      $scope.progress = 0;
      $scope.uploadComplete = false;
      $scope.uploadError = false;
      $scope.$apply();
      $scope.uploadInChunks();
    };

    $scope.uploadInChunks = function() {
      const file = $scope.selectedFile;
      const chunkSize = 15 * 1024 * 1024;
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
        formData.append('fileId', $scope.fileId);
        formData.append('fileType', 'video');

        // No actualizar aquí, actualizamos cuando se recibe el .then()

        $http.post(routing.generate('api_v1_backend_storage_upload_chunk'), formData, {
          // eslint-disable-next-line no-undefined
          headers: { 'Content-Type': undefined },
          transformRequest: angular.identity
        }).then(function(response) {
          // ✅ Sumamos bytes enviados
          uploadedBytes += end - start;
          $scope.progress = Math.floor(uploadedBytes / file.size * 100);
          $scope.$applyAsync();

          currentChunk++;

          if (currentChunk < totalChunks) {
            $scope.sendNextChunk();
          } else {
            if (response.data.status === 'done') {
              $scope.progress = 100;
              $scope.uploadComplete = true;
              $scope.filePath = response.data.filePath;
            }
            $scope.$applyAsync();
          }
        }, function(error) {
          //console.error('Error subiendo chunk:', error);
          $scope.uploadError = true;
          $scope.$applyAsync();
        });
      };
      $scope.sendNextChunk();
    };
  }
]);
