(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  BaseCtrl
     *
     * @requires $rootScope
     * @requires $scope
     * @requires $timeout
     * @requires Editor
     * @requires messenger
     * @requires Renderer
     *
     * @description
     *   This controller provides all basic actions that controllers for list
     *   and inners will need. All controllers should extend this.
     */
    .controller('BaseCtrl', [
      '$rootScope', '$scope', '$timeout', '$uibModal', '$window', 'Editor',
      'http', 'linker', 'localizer', 'messenger', 'Renderer', '$sce',
      function($rootScope, $scope, $timeout, $uibModal, $window, Editor, http, linker, localizer, messenger, Renderer, $sce) {
        /**
         * @memberOf BaseCtrl
         *
         * @description
         *  An object to backup properties.
         *
         * @type {Object}
         */
        $scope.backup = {};

        /**
         * @memberOf BaseCtrl
         *
         * @description
         *  The list configuration.
         *
         * @type {Object}
         */
        $scope.config = {
          linkers: {},
          locale: null,
          multilanguage: null,
          translators: []
        };

        /**
         * @memberOf BaseCtrl
         *
         * @description
         *  The list of flags.
         *
         * @type {Object}
         */
        $scope.flags = {
          block: { slug: true },
          generate: {},
          http: {},
          visible: { grid: true }
        };

        /**
         * @memberOf InnerCtrl
         *
         * @description
         *  The list of overlays.
         *
         * @type {Object}
         */
        $scope.overlay = {};

        /**
         * @memberOf InnerCtrl
         *
         * @description
         *  List of tags by id
         *
         * @type {Array}
         */
        $scope.tags = {};

        /**
         * @memberOf BaseCtrl
         *
         * @description
         *  The object to use as target for media/content pickers.
         *
         * @type {Object}
         */
        $scope.target = {};

        /**
         * @memberOf BaseCtrl
         *
         * @description
         *  Temporal scope. It should be used only with objects needed in the
         *  UI and information in it should never be sent to the server.
         *
         * @type {Object}
         */
        $scope.tmp = {};

        /**
         * @function BaseCtrl
         * @memberOf availableItemsInGrid
         *
         * @description
         *   Returns the number of items that can be shown in grid mode basing
         *   on the window size.
         *
         * @return {Integer} The number of items per page.
         */
        $scope.getEppInGrid = function() {
          var padding   = 15;
          var maxHeight = $(window).height() - $('.header').height() -
            $('.actions-navbar').height() * 2 - padding;
          var maxWidth  = $(window).width() - $('.sidebar').width();

          if ($('.content-wrapper').length > 0) {
            maxWidth -= parseInt($('.content-wrapper').css('padding-right'));
          }

          var containerBaseSize = 150;
          var containerSize = $('.infinite-col').width();

          if (containerBaseSize > containerSize) {
            containerSize = containerBaseSize;
          }

          var height = containerSize + padding;
          var width = containerSize + padding;

          var rows = Math.ceil(maxHeight / height);
          var cols = Math.floor(maxWidth / width);

          if (rows === 0) {
            rows = 1;
          }

          if (cols === 0) {
            cols = 1;
          }

          if ($scope.criteria.epp !== rows * cols && $scope.data) {
            $scope.data.items = [];
          }

          return rows * cols;
        };

        /**
         * @function removeImage
         * @memberOf InnerCtrl
         *
         * @description
         *   Removes the given image from the scope.
         *
         * @param string image The image to remove.
         */
        $scope.removeImage = function(image) {
          delete $scope[image];
        };

        /**
         * @function removeItem
         * @memberOf InnerCtrl
         *
         * @description
         *   Removes an item from an array of related items.
         *
         * @param string  from  The array name in the current scope.
         * @param integer index The index of the element to remove.
         */
        $scope.removeItem = function(from, index) {
          var keys  = from.split('.');
          var model = $scope;

          for (var i = 0; i < keys.length - 1; i++) {
            if (!model[keys[i]]) {
              model[keys[i]] = {};
            }

            model = model[keys[i]];
          }

          if (angular.isArray(model[keys[i]])) {
            model[keys[i]].splice(index, 1);
            return;
          }

          model[keys[i]] = null;
        };

        /**
         * @function toggleMode
         * @memberOf BaseCtrl
         *
         * @description
         *   Changes the current mode.
         */
        $scope.setMode = function(mode) {
          if ($scope.app.mode === mode) {
            return;
          }

          $scope.app.mode = mode;
        };

        /**
         * @function toggleOverlay
         * @memberOf InnerCtrl
         *
         * @description
         *   Enables/disables an overlay by name.
         *
         * @param {String} name The overlay name.
         */
        $scope.toggleOverlay = function(name) {
          $scope.overlay[name] = !$scope.overlay[name];
        };

        /**
         * @function configure
         * @memberOf BaseCtrl
         *
         * @description
         *   Configures the language for the current section.
         *
         * @param {Object} data The data to configure the section.
         */
        $scope.configure = function(data) {
          if (!data) {
            return;
          }

          if (data.locale) {
            $scope.config.locale = data.locale;
          }

          if ($scope.forcedLocale && Object.keys(data.locale.available)
            .indexOf($scope.forcedLocale) !== -1) {
            // Force localization
            $timeout(function() {
              $scope.config.locale.selected = $scope.forcedLocale;
            });
          }
        };

        /**
         * @function disableFlags
         * @memberOf BaseCtrl
         *
         * @description
         *   Disables all flags.
         *
         * @param {String} group The name for the group of flags.
         */
        $scope.disableFlags = function(group) {
          var flags = $scope.flags;

          if (group) {
            flags = flags[group];
          }

          for (var key in flags) {
            flags[key] = angular.isObject(flags[key]) ? {} : false;
          }
        };

        /**
         * @function errorCb
         * @memberOf BaseCtrl
         *
         * @description
         *   The callback function to execute when an ajax request fails.
         *
         * @param {Object} response The response object.
         */
        $scope.errorCb = function(response) {
          $scope.disableFlags('http');

          if (response && response.data) {
            messenger.post(response.data);
          }
        };

        /**
         * @function getL10nUrl
         * @memberOf BaseCtrl
         *
         * @description
         *   Returns the localized url given the multilanguage and current url status
         *
         * @param {String}   url     The value of the url to "localize".
         */
        $scope.getL10nUrl = function(url) {
          var baseUrl = '';

          if ($scope.data && $scope.data.extra.locale.multilanguage &&
              $scope.data.extra.locale.default !== $scope.config.locale.selected) {
            baseUrl = '/' + $scope.config.locale.slugs[$scope.config.locale.selected];
          }

          return baseUrl + url;
        };

        /**
         * @function getSlug
         * @memberOf BaseCtrl
         *
         * @description
         *   Request a slug to the server.
         *
         * @param {String}   slug     The value to calculate slug from.
         * @param {Function} callback The callback to execute on success.
         */
        $scope.getSlug = function(slug, callback) {
          $scope.flags.http.slug = 1;

          http.get({
            name: 'api_v1_backend_tools_slug',
            params: { slug: slug }
          }).then(function(response) {
            $scope.disableFlags('http');

            var getType = {};

            if (callback && getType.toString.call(callback) === '[object Function]') {
              return callback(response);
            }
            return null;
          }, function() {
            $scope.disableFlags('http');
          });
          return null;
        };

        /**
         * @function hasMultilanguage
         * @memberOf BaseCtrl
         *
         * @description
         *   Checks if the section managed by this controller has multilanguage
         *   support.
         *
         * @return {Boolean} True if the section supports multilanguage. False
         *                   otherwise.
         */
        $scope.hasMultilanguage = function() {
          return false;
        };

        /**
         * @function insertInCKEditor
         * @memberOf BaseCtrl
         *
         * @description
         *   Inserts an array of items in a CKEditor instance.
         *
         * @param string target The target id.
         * @param array  items  The items to insert.
         */
        $scope.insertInCKEditor = function(target, items) {
          if (!(items instanceof Array)) {
            items = [ items ];
          }

          for (var i = 0; i < items.length; i++) {
            if (items[i].content_type_name === 'photo') {
              Editor.get(target).insertHtml(Renderer.renderImage(items[i]));
            } else {
              Editor.get(target).insertHtml(Renderer.renderContent(items[i]));
            }
          }

          Editor.get(target).fire('change');
        };

        /**
         * @function insertInModel
         * @memberOf BaseCtrl
         *
         * @description
         *   Updates the scope with the items.
         *
         * @param string target The property to update.
         * @param array  items  The new property value.
         */
        $scope.insertInModel = function(target, items) {
          $scope.loaded = false;

          var keys  = target.split('.');
          var model = $scope;

          for (var i = 0; i < keys.length - 1; i++) {
            if (!model[keys[i]]) {
              model[keys[i]] = {};
            }

            model = model[keys[i]];
          }

          model[keys[i]] = items;

          // Trick to force dynamic image re-rendering
          $timeout(function() {
            $scope.loaded = true;
          }, 0);
        };

        /**
         * @function isModeSupported
         * @memberOf BaseCtrl
         *
         * @description
         *   Checks if the mode is supported in the current controller.
         *
         * @return {Boolean} True if the mode is supported in the current
         *                   controller. False otherwise.
         */
        $scope.isModeSupported = function() {
          return false;
        };

        /**
         * @function launchPhotoEditor
         * @memberOf InnerCtrl
         *
         * @description
         *   launch the photo editor.
         *
         * @param {String} locale The locale to check.
         *
         * @return {Boolean} True if the article is translated. False otherwise.
         */
        $scope.launchPhotoEditor = function(imgData) {
          var modal = $uibModal.open({
            template: '<div id="photoEditor" class="photoEditor"><div>',
            backdrop: 'static',
            windowClass: 'modal-photo-editor'
          });

          modal.rendered.then(function() {
            var photoEditor = new window.OnmPhotoEditor({
              container: 'photoEditor',
              image: $window.instanceMedia + imgData.path,
              closeCallBack: modal.close,
            }, photoEditorTranslations);

            photoEditor.init();
          });

          modal.result.then(function(image) {
            $scope.uploadMediaImg(image, imgData);
          });
        };

        /**
         * @function trustHTML
         * @memberOf BaseCtrl
         *
         * @description
         *   Marks the text as trusted to be embed it as HTML.
         */
        $scope.trustHTML = function(src) {
          return $sce.trustAsHtml(src);
        };

        /**
         * @function trustSrc
         * @memberOf BaseCtrl
         *
         * @description
         *   Marks the text as trusted to be embed it as URL.
         */
        $scope.trustSrc = function(src) {
          return $sce.trustAsResourceUrl(src);
        };

        /**
         * @function localize
         * @memberOf BaseCtrl
         *
         * @description
         *   Configures multilanguage-related services basing on the scope.
         *
         * @param {Object} The item or list of items to localize.
         * @param {String} The name of the property where localized items will
         *                 be stored in scope.
         */
        $scope.localize = function(items, key, clean, ignore) {
          var lz = localizer.get($scope.config.locale);

          // Localize items
          $scope[key] = lz.localize(items,
            $scope.data.extra.keys, $scope.config.locale);

          // Initialize linker
          if (!$scope.config.linkers[key]) {
            $scope.config.linkers[key] = linker.get($scope.data.extra.keys,
              $scope.config.locale.default, $scope, clean, ignore);
          }

          // Link original and localized items
          $scope.config.linkers[key].setKey($scope.config.locale.selected);
          $scope.config.linkers[key].link(items, $scope[key]);
        };

        $scope.uploadMediaImg = function(image, imgData) {
          if (image === null) {
            return null;
          }

          var route = { name: 'api_v1_backend_photo_save_item' };
          var body  = {};

          body['imgData.name'] = image;

          http.post(route, body).then(function() {
            if (typeof $scope.list === 'function') {
              $scope.list($scope.route, true);
            }
          }, function() {
            return null;
          });

          return null;
        };

        /**
         * @function getFeaturedMedia
         * @memberOf BaseCtrl
         *
         * @description
         *   Returns the featured media of type for an item.
         *
         * @param {Object} The item to get featured media for.
         * @param {String} The featured media type.
         */
        $scope.getFeaturedMedia = function(item, type) {
          if (!item.related_contents) {
            return { path: null };
          }

          var featured = item.related_contents.filter(function(e) {
            return e.type === type;
          });

          if (featured.length === 0 ||
              !$scope.data.extra.related_contents[featured[0].target_id]) {
            return { path: null };
          }

          return $scope.data.extra.related_contents[featured[0].target_id];
        };

        /**
         * Insert the selected items in media picker in the target element.
         *
         * @param  Object event The event object.
         * @param  Object args  The event arguments.
         */
        $rootScope.$on('MediaPicker.insert', function(event, args) {
          if (/editor.*/.test(args.target)) {
            var target = args.target.replace('editor.', '');

            $scope.insertInCKEditor(target, args.items);
            return;
          }

          $scope.insertInModel(args.target, args.items);
        });

        /**
         * Insert the selected items in media picker in the target element.
         *
         * @param  Object event The event object.
         * @param  Object args  The event arguments.
         */
        $rootScope.$on('ContentPicker.insert', function(event, args) {
          if (/editor.*/.test(args.target)) {
            var target = args.target.replace('editor.', '');

            $scope.insertInCKEditor(target, args.items);
            return;
          }

          $scope.insertInModel(args.target, args.items);
        });

        // Updates linkers when locale changes
        $scope.$watch('config.locale.selected', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if (!$scope.config.locale.multilanguage ||
              !$scope.config.locale.selected) {
            return;
          }

          for (var key in $scope.config.linkers) {
            $scope.config.linkers[key].setKey(nv);
            $scope.config.linkers[key].update();
          }
        }, true);
      }
    ]);
})();
