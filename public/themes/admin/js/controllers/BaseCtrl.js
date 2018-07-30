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
      '$rootScope', '$scope', '$timeout', '$uibModal', '$window', 'Editor', 'http', 'messenger', 'Renderer',
      function($rootScope, $scope, $timeout, $uibModal, $window, Editor, http, messenger, Renderer) {
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
          columns: {
            collapsed: true,
            selected: []
          },
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
        $scope.flags = { http: {} };

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
         * @memberOf InnerCtrl
         *
         * @description
         *  List of suggested tags
         *
         * @type {Array}
         */
        $scope.suggestedTags = {};

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

          // Configure the form
          if ($scope.config.multilanguage === null) {
            $scope.config.multilanguage = data.multilanguage;
          }

          if (data.translators) {
            $scope.config.translators = data.translators;
          }

          if ($scope.config.locale === null) {
            $scope.config.locale = data.locale;
          }

          if ($scope.forcedLocale && Object.keys(data.options.available)
            .indexOf($scope.forcedLocale)) {
            $scope.config.locale = $scope.forcedLocale;
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

            if (callback &&
                getType.toString.call(callback) === '[object Function]') {
              return callback(response);
            }
            return null;
          }, function() {
            $scope.disableFlags('http');
          });
          return null;
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
            Editor.get(target).insertHtml(Renderer.renderImage(items[i]));
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
         * @function isTranslated
         * @memberOf BaseCtrl
         *
         * @description
         *   Checks if the article is translated to the locale.
         *
         * @param {String} locale The locale to check.
         *
         * @return {Boolean} True if the article is translated. False otherwise.
         */
        $scope.isTranslated = function(item, keys, locale) {
          for (var i = 0; i < keys.length; i++) {
            if (item[keys[i]] && item[keys[i]][locale]) {
              return true;
            }
          }

          return false;
        };

        /**
         * @function getSuggestedTags
         * @memberOf BaseCtrl
         *
         * @description
         *   Method to retrive all tags suggested tags from different fields.
         *
         * @param {Array} list of fields
         *
         * @return {Array} list of suggested tags from the fields
         */
        $scope.getSuggestedTags = function(locale, tagText, currentTags) {
          var route = {
            name: 'api_v1_backend_tags_suggester',
            params: {
              tag: tagText,
              languageId: locale
            }
          };

          return http.get(route).then(
            function(response) {
              if (!response.data.items || !Array.isArray(response.data.items)) {
                return [];
              }

              /*
               *  We check if from the suggested tags exist someones in the
               * current tag list
               */
              var tagsuggestedList = Array.isArray(currentTags) && currentTags.length > 0 ?
                response.data.items.filter(function(tagElement) {
                  return currentTags.indexOf(tagElement.id) === -1;
                }) :
                response.data.items;

              return tagsuggestedList;
            }, $scope.errorCb
          );
        };

        /**
         * @function checkNewTags
         * @memberOf BaseCtrl
         *
         * @description
         *   Check if the some tags exist or not in the DB
         *
         * @param function - function to apply the needed changes for the response
         * @param String   - tag2Check tag to check
         * @param String   - locale tag language
         * @param int      - id of the actual tag if have one
         *
         * @return {Array} List of tags associate to his DB id
         */
        $scope.checkNewTags = function(callback, tag2Check, locale, id) {
          if ($scope.tm) {
            $timeout.cancel($scope.tm);
          }

          $scope.tm = $timeout(function() {
            if (tag2Check.length < 2) {
              return false;
            }
            var route = {
              name: 'api_v1_backend_tags_valid_new_tag',
              params: {
                text: tag2Check,
                languageId: locale
              }
            };

            http.get(route).then(
              function(response) {
                if (!response.data.valid) {
                  callback({ error: response.data.message });
                  return null;
                }
                callback(response.data.valid);
                return null;
              }, function(response) {
                callback({ error: response.data.message });
                return null;
              }
            );
            return null;
          }, 500);
        };

        /**
         * @function checkAutoSuggesterTags
         * @memberOf BaseCtrl
         *
         * @description
         *   Check if the some tags exist or not in the DB
         *
         * @param function - applyChanges to apply the needed changes for the response
         * @param String   - tag2Check tag to check
         * @param String   - locale tag language
         *
         * @return {Array} List of suggested tags
         */
        $scope.checkAutoSuggesterTags = function(callback, text2Check, currentTags, locale) {
          var text2Check = text2Check.replace(/[^0-9A-zÀ-ú_\-\s]/gi, '');
          var tagsVal = text2Check.split(' ').filter(function(tag) {
            return tag.length > 1;
          });

          // Remove dupes
          tagsVal = tagsVal.filter(function(el, i, a) {
            return i === a.indexOf(el);
          });

          var successCallback = function(response) {
            if (!response.data.items || response.data.items === null) {
              return null;
            }
            var items                = response.data.items;
            var newTagsInCurrentTags = [];

            $scope.suggestedTags = [];

            for (var j = 0; j < currentTags.length; j++) {
              if (typeof currentTags[j] === 'object') {
                newTagsInCurrentTags.push(currentTags[j].name);
              }
            }

            var newTags = [];

            for (var i = 0; i < items.length; i++) {
              if (items[i].id) {
                if (!(items[i].id in $scope.tags)) {
                  $scope.tags[items[i].id] = items[i];
                }
                if (currentTags.indexOf(items[i].id) === -1) {
                  newTags.push(items[i].id);
                }
              } else if (newTagsInCurrentTags.indexOf(items[i].name) === -1) {
                $scope.suggestedTags.push(items[i]);
              }
            }

            callback(newTags);
            return null;
          };

          $scope.newAndExistingTagsFromTagList(tagsVal, locale, successCallback);
          return null;
        };

        /**
         * @function newAndExistingTagsFromTagList
         * @memberOf BaseCtrl
         *
         * @description
         *   From a list of words return the tag for this word or a new tag if not exist
         *
         * @param function - applyChanges to apply the needed changes for the response
         * @param String   - tag2Check tag to check
         * @param String   - locale tag language
         *
         * @return {Array} List of suggested tags
         */
        $scope.newAndExistingTagsFromTagList = function(tagsVal, locale, callback) {
          var route = {
            name: 'api_v1_backend_tags_auto_suggester',
            params: {
              tags: tagsVal,
              languageId: locale
            }
          };

          http.get(route).then(
            callback, $scope.errorCb
          );
        };

        /**
         * @function loadAutoSuggestedTags
         * @memberOf BaseCtrl
         *
         * @description
         *   Retrieve all auto suggested words for the content
         *
         * @return {string} all words for the title
         */
        $scope.loadAutoSuggestedTags = function() {
          if (typeof $scope.getTagsAutoSuggestedFields === 'undefined') {
            return null;
          }
          var data = $scope.getTagsAutoSuggestedFields();

          $scope.checkAutoSuggesterTags(
            function(items) {
              if (items !== null) {
                $scope.tag_ids = $scope.tag_ids.concat(items);
              }
            },
            data,
            $scope.tag_ids,
            $scope.locale
          );
          return null;
        };

        /**
         * @function watchTagIds
         * @memberOf BaseCtrl
         *
         * @description
         * Updates scope when the tag related fields changes.
         *
         * @param mixed fields Fields to watch
         */
        $scope.watchTagIds = function(fields) {
          $scope.$watch(fields, function(nv, ov) {
            if ($scope.tag_ids && $scope.tag_ids.length > 0 ||
                !nv || nv === ov) {
              return;
            }

            if ($scope.mtm) {
              $timeout.cancel($scope.mtm);
            }

            $scope.mtm = $timeout(function() {
              $scope.loadAutoSuggestedTags();
            }, 2500);
          });
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
              image: $window.instanceMedia + '/images' + imgData.path_img,
              closeCallBack: modal.close,
            },
            photoEditorTranslations
            );

            photoEditor.init();
          });

          modal.result.then(function(image) {
            $scope.uploadMediaImg(image, imgData);
          });
        };

        $scope.uploadMediaImg = function(image, imgData) {
          if (image === null) {
            return null;
          }

          var route = { name: 'admin_image_create' };
          var body  = {};

          body[imgData.name] = image;
          http.post(route, body).success(function() {
            if (typeof $scope.list === 'function') {
              $scope.list($scope.route, true);
            }
          }).error(function() {
            return null;
          });
          return null;
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
        $scope.$watch('config.locale', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if (!$scope.config.multilanguage || !$scope.config.locale) {
            return;
          }

          for (var key in $scope.config.linkers) {
            $scope.config.linkers[key].setKey(nv);
            $scope.config.linkers[key].update();
          }
        });
      }
    ]);
})();
