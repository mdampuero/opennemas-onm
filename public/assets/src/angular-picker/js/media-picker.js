(function() {
  'use strict';

  angular.module('onm.picker')

    /**
     * @ngdoc directive
     * @name  mediaPicker
     *
     * @requires $compile
     * @requires $window
     * @requires http
     *
     * @description
     *   Directive to create and display the media picker modal window.
     */
    .directive('mediaPicker', [
      '$compile', '$window', 'http',
      function($compile, $window, http) {
        // Runs during compile
        return {
          controller: 'MediaPickerCtrl',
          restrict: 'A',
          scope: {
            mediaPickerIgnore: '=',
            mediaPickerTarget: '='
          },
          link: function($scope, elm, attrs) {
            /**
             * Template for the media picker content.
             *
             * @type string
             */
            var contentTpl = {
              explore: '<div class="picker-panel explore-panel" ng-class="{ \'active\': picker.isModeActive(\'explore\') }">' +
                '<div class="picker-panel-header clearfix">' +
                  '<h4 class="pull-left">[% picker.params.explore.header %]</h4>' +
                '</div>' +
                '<div class="picker-panel-body">' +
                  '<div class="picker-panel-topbar">' +
                    '<ul>' +
                      '<li>' +
                        '<div class="controls">' +
                          '<div class="input-group">' +
                            '<span class="input-group-addon">' +
                              '<i class="fa fa-search"></i>' +
                            '</span>' +
                            '<input ng-model="criteria.title" placeholder="[% picker.params.explore.search %]" type="text"/>' +
                          '</div>' +
                        '</div>' +
                      '</li>' +
                      '<li ng-if="picker.isTypeEnabled(\'photo\')">' +
                        '<select name="month" ng-model="criteria.created">' +
                          '<option value="">[% picker.params.explore.allMonths %]</option>' +
                          '<optgroup label="[% year.name %]" ng-repeat="year in picker.params.explore.dates">' +
                            '<option value="[% month.value %]" ng-repeat="month in year.months">' +
                              '[% month.name %] ([% year.name %])' +
                            '</option>' +
                          '</optgroup>' +
                        '</select>' +
                      '</li>' +
                      '<li class="hidden-xs" ng-if="picker.isTypeEnabled(\'video\')">' +
                        '<onm-category-selector default-value-text="[% picker.params.explore.any %]" label-text="[% picker.params.explore.category %]" ng-model="$parent.$parent.category" placeholder="[% picker.params.explore.any %]"></onm-category-selector>' +
                      '</li>' +
                    '</ul>' +
                  '</div>' +
                  '<div class="picker-panel-wrapper">' +
                    '<div class="picker-panel-content" when-scrolled="scroll()">' +
                      '<div class="clearfix items" ng-if="!searchLoading">' +
                        '<div class="media-item" ng-repeat="item in uploader.queue">' +
                          '<div class="img-thumbnail">' +
                            '<i class="fa fa-picture-o fa-5x"></i>' +
                            '<div class="progress" style="margin-bottom: 0;">' +
                              '<div class="progress-bar" role="progressbar" ng-style="{ \'width\': item.progress + \'%\' }"></div>' +
                            '</div>' +
                          '</div>' +
                        '</div>' +
                        '<div class="media-item"[selection] ng-repeat="content in contents track by $index" style="width: 120px;">' +
                          '<dynamic-image only-image="true" class="img-thumbnail" instance="' +
                            $window.instanceMedia +
                             '" ng-if="content.content_type_name == \'photo\'" ng-model="content" width="80" transform="zoomcrop,120,120,center,center"></dynamic-image>' +
                          '<dynamic-image only-image="true" class="img-thumbnail" ng-if="content.content_type_name == \'video\'" instance="' +
                          $window.instanceMedia +
                          '" ng-model="content.pk_content" transform="zoomcrop,120,120"></dynamic-image>' +
                        '</div>' +
                      '</div>' +
                      '<div class="text-center m-b-30 p-t-15 p-b-30 pointer" ng-click="scroll()" ng-if="!searchLoading && total != contents.length">' +
                        '<h5>' +
                          '<i class="fa fa-circle-o-notch fa-spin fa-lg" ng-if="loadingMore"></i>' +
                          '<span ng-if="!loadingMore">[% picker.params.explore.loadMore %]</span>' +
                          '<span ng-if="loadingMore">[% picker.params.explore.loading %]</span>' +
                        '</h5>' +
                      '</div>' +
                      '<div class="items-loading" ng-if="searchLoading">' +
                        '<i class="fa fa-circle-o-notch fa-spin fa-4x"></i>' +
                      '</div>' +
                    '</div>' +
                  '</div>' +
                  '<div class="picker-panel-sidebar">' +
                    '<div class="picker-panel-sidebar-header">' +
                      '<h4>[% picker.params.explore.pk_contentDetails %]</h4>' +
                    '</div>' +
                    '<div class="picker-panel-sidebar-body" ng-if="selected.lastSelected">' +
                      '<div class="media-thumbnail-wrapper" ng-if="selected.lastSelected.content_type_name == \'photo\' && !isFlash(selected.lastSelected)">' +
                        '<dynamic-image autoscale="true" instance="' +
                          $window.instanceMedia +
                          '" ng-model="selected.lastSelected" transform="thumbnail,220,220">' +
                        '</dynamic-image>' +
                      '</div>' +
                      '<div class="media-thumbnail-wrapper" ng-if="selected.lastSelected.content_type_name == \'video\'">' +
                        '<dynamic-image autoscale="true" instance="' +
                          $window.instanceMedia +
                          '" ng-model="selected.lastSelected.pk_content" transform="thumbnail,220,220">' +
                        '</dynamic-image>' +
                      '</div>' +
                      '<ul class="media-information">' +
                        '<li>' +
                          '<a ng-href="[% selected.lastSelected.content_type_name === \'photo\' ? routing.generate(\'backend_photo_show\', { id: selected.lastSelected.pk_content}) : routing.generate(\'backend_video_show\', { id: selected.lastSelected.pk_content})%]">' +
                            '<strong>' +
                              '[% selected.lastSelected.title %]' +
                              '<i class="fa fa-edit"></i>' +
                            '</strong>' +
                          '</a>' +
                        '</li>' +
                        '<li ng-if="picker.isPhotoEditorEnabled()">' +
                          '<a class="btn btn-primary ng-isolate-scope" ng-click="enhanceAction()">' +
                              '<i class="fa fa-sliders"></i>' +
                              '[% picker.params.explore.enhance %]' +
                          '</a>' +
                        '</li>' +
                        '<li>[% selected.lastSelected.created | moment %]</li>' +
                        '<li ng-if="selected.lastSelected.content_type_name === \'photo\'">[% selected.lastSelected.size %] KB</li>' +
                        '<li ng-if="selected.lastSelected.content_type_name === \'photo\'">[% selected.lastSelected.width %] x [% selected.lastSelected.height %]</li>' +
                        '<li><span class="v-seperate"></span></li>' +
                        '<li>' +
                          '<div class="form-group">' +
                            '<label for="description">' +
                              '[% picker.params.explore.description %]' +
                              '<div class="pull-right">' +
                                '<i class="fa" ng-class="{ \'fa-circle-o-notch fa-spin\': saving, \'fa-check text-success\': saved, \'fa-times text-danger\': error }"></i>' +
                              '</div>' +
                            '</label>' +
                            '<textarea id="description" ng-blur="saveDescription(selected.lastSelected.pk_content)" ng-model="selected.lastSelected.description" cols="30" rows="2"></textarea>' +
                          '</div>' +
                        '</li>' +
                      '</ul>' +
                    '</div>' +
                  '</div>' +
                '</div>' +
                '<div class="picker-panel-footer" ng-class="{ \'collapsed\': selected.items.length == 0 && !uploadError, \'danger\': uploadError }">' +
                  '<ul class="pull-left" ng-show="selected.items.length > 0 || uploadError">' +
                    '<li>' +
                      '<i class="fa fa-lg" ng-class="{ \'fa-check\': !uploadError, \'fa-times\': uploadError } "ng-click="selected.ids = [];selected.items = [];uploadError = null"></i>' +
                    '</li>' +
                    '<li>' +
                      '<span class="h-seperate"></span>' +
                    '</li>' +
                    '<li>' +
                      '<h4 ng-if="!uploadError && selected.items.length">' +
                        '[% selected.items.length %]' +
                        '<span class="hidden-xs m-l-5">[% picker.params.explore.itemsSelected %]</span>' +
                      '</h4>' +
                      '<h4 ng-if="uploadError">' +
                        '<span class="hidden-xs">[% picker.params.explore.error %]</span>' +
                      '</h4>' +
                    '</li>' +
                  '</ul>' +
                  '<button class="btn btn-primary pull-right" ng-click="insert()" ng-if="!uploadError">' +
                    '<i class="fa fa-plus"></i>' +
                    '[% picker.params.explore.insert %]' +
                  '</button>' +
                '</div>' +
              '</div>',

              upload: '<div class="picker-panel upload-panel" ng-class="{ \'active\': picker.isModeActive(\'upload\') }">' +
                '<div class="picker-panel-header clearfix">' +
                  '<h4 class="pull-left">[% picker.params.upload.header %]</h4>' +
                '</div>' +
                '<div class="picker-panel-body">' +
                  '<div class="picker-panel-wrapper">' +
                    '<div class="picker-panel-content">' +
                      '<div class="drop-zone-text">' +
                        '<h4>' +
                          '<div>' +
                            '<i class="fa fa-picture-o fa-2x" ng-if="picker.isTypeEnabled(\'photo\')"></i>' +
                            '<i class="fa fa-film fa-2x" ng-if="picker.isTypeEnabled(\'video\')"></i>' +
                            '<i class="fa fa-file-o fa-2x" ng-if="picker.isTypeEnabled(\'pdf\')"></i>' +
                          '</div>' +
                          '<span class="hidden-xs">[% picker.params.upload.drop %]</span>' +
                          '<span class="visible-xs">[% picker.params.upload.click %]</span>' +
                        '</h4>' +
                        '<h5 class="hidden-xs">' +
                          '[% picker.params.upload.upload %]' +
                        '</h5>' +
                      '</div>' +
                    '</div>' +
                    '<input type="file" nv-file-select uploader="uploader" multiple/>' +
                  '</div>' +
                '</div>' +
                '<div class="picker-panel-footer picker-panel-footer-full picker-panel-footer-error" ng-class="{ \'collapsed\': !invalid }">' +
                  '<ul class="pull-left">' +
                    '<li>' +
                      '<i class="fa fa-times fa-lg"></i>' +
                    '</li>' +
                    '<li>' +
                      '<span class="h-seperate"></span>' +
                    '</li>' +
                    '<li>' +
                      '<h4>' +
                        '<span class="hidden-xs">[% picker.params.upload.invalid %]</span>' +
                      '</h4>' +
                    '</li>' +
                  '</ul>' +
                '</div>' +
              '</div>',
            };

            /**
             * Template for the media picker.
             *
             * @type string
             */
            var pickerTpl = '<div class="picker">' +
              '<div class="picker-backdrop"></div>' +
              '<div class="picker-dialog">' +
                  '<div ng-hide="!enhance">' +
                    '<div id="photoEditor" class="photoEditor"></div>' +
                  '</div>' +
                  '<div class="picker-close" ng-click="close()" ng-hide="enhance">' +
                    '<i class="fa fa-lg fa-times pull-right"></i>' +
                  '</div>' +
                  '<div class="picker-loading" ng-if="loading" ng-hide="enhance">' +
                    '<i class="fa fa-circle-o-notch fa-spin fa-4x"></i>' +
                  '</div>' +
                  '<div class="picker-sidebar" ng-if="!loading" ng-hide="enhance">' +
                    '<ul>' +
                      '[sidebar]' +
                    '</ul>' +
                  '</div>' +
                  '<div class="picker-content" ng-if="!loading" ng-hide="enhance">' +
                    '[content]' +
                  '</div>' +
                  '<div class="picker-loading" ng-hide="!enhance">' +
                    '<div id="photoEditor"></div>' +
                  '</div>' +
                '</div>' +
              '</div>' +
            '</div>';

            /**
             * Template for the media picker sidebar items.
             *
             * @type string
             */
            var sidebarTpl = {
              explore: '<li ng-class="{ \'active\': picker.isModeActive(\'explore\') }" ng-click="picker.enable(\'explore\'); explore()">' +
                '<h5>' +
                  '<i class="fa fa-folder"></i>' +
                  '[% picker.params.explore.menuItem %]' +
                '</h5>' +
              '</li>',

              upload: '<li ng-class="{ \'active\': picker.isModeActive(\'upload\') }" ng-click="picker.enable(\'upload\'); upload()">' +
                '<h5>' +
                  '<i class="fa fa-upload"></i>' +
                  '[% picker.params.upload.menuItem %]' +
                '</h5>' +
              '</li>'
            };

            /**
             * Default media picker configuration.
             *
             * @type Object
             */
            $scope.picker = {
              modes: {
                active:    'explore',
                available: [ 'upload', 'explore' ],
                enabled:   [ 'explore' ]
              },
              selection: {
                enabled: attrs.mediaPickerSelection === 'true',
                maxSize: attrs.mediaPickerMaxSize ?
                  parseInt(attrs.mediaPickerMaxSize) : 1
              },
              target: attrs.mediaPickerTarget,
              types: {
                enabled:   [ 'photo' ],
                available: [ 'photo', 'video' ]
              },
              photoEditorEnabled: attrs.photoEditorEnabled && attrs.photoEditorEnabled === 'true',

              /**
               * Closes the current media picker.
               */
              close: function() {
                // Reset html and body
                $('html, body').removeClass('picker-open');

                // Delete the current media picker
                $('.picker').remove();
                $('.picker-backdrop').remove();
              },

              /**
               * Checks if the given module is currently active.
               *
               * @param string mode The mode to check.
               *
               * @return boolean Returns true if the given mode is currently
               *                 active. Otherwise, returns false.
               */
              isModeActive: function(mode) {
                return this.modes.active === mode;
              },

              /**
               * Checks if a mode is enabled.
               *
               * @param string mode The mode to check.
               *
               * @return boolean True if the given mode is enabled. Otherwise,
               *                 returns false.
               */
              isModeEnabled: function(mode) {
                return this.modes.enabled.indexOf(mode) !== -1;
              },

              /**
               * Checks if a content type is enabled.
               *
               * @param string type The content type to check.
               *
               * @return boolean True if the given content type is enabled.
               *                 Otherwise, returns false.
               */
              isTypeEnabled: function(type) {
                return this.types.enabled.indexOf(type) !== -1;
              },

              /**
               * Checks if a content type is enabled.
               *
               * @param string type The content type to check.
               *
               * @return boolean True if the given content type is enabled.
               *                 Otherwise, returns false.
               */
              isTypeOnlyEnabled: function(type) {
                return this.types.enabled.indexOf(type) !== -1 &&
                  this.types.enabled.length === 1;
              },

              /**
               * Enables the given mode.
               *
               * @param string mode The mode to enable.
               */
              enable: function(mode) {
                this.modes.active = mode;
              },

              /**
               * Checks if the photo editor is enabled.
               */
              isPhotoEditorEnabled: function() {
                return this.photoEditorEnabled;
              },

              /**
               * Renders the media picker.
               */
              render: function() {
                var content = '';
                var picker  = pickerTpl;
                var selection = '';
                var sidebar = '';

                for (var i = 0; i < this.modes.enabled.length; i++) {
                  sidebar += sidebarTpl[this.modes.enabled[i]];
                  content += contentTpl[this.modes.enabled[i]];
                }

                // Add selection actions
                if (this.selection.enabled) {
                  selection  = 'ng-class="{ ' +
                    '\'selected\': isSelected(content), ' +
                    '\'ignored\': isIgnored(content), ' +
                    '\'selectable\': isSelectable(content) ' +
                    '}" ng-click="toggle(content, $event)"';
                }

                content = content.replace(/\[selection\]/g, selection);

                picker = picker.replace(/\[sidebar\]/g, sidebar);
                picker = picker.replace(/\[content\]/g, content);

                return picker;
              },

              /**
               * Sets the media picker content modes.
               *
               * @param string mode The content mode.
               */
              setMode: function(mode) {
                if (this.modes.available.indexOf(mode) !== -1 &&
                    this.modes.enabled.indexOf(mode) === -1) {
                  this.modes.enabled.push(mode);
                }
              },

              /**
               * Sets the media picker content types.
               *
               * @param string type The content type.
               */
              setType: function(type) {
                if (this.types.available.indexOf(type) !== -1 &&
                    this.types.enabled.indexOf(type) === -1) {
                  this.types.enabled.push(type);
                }
              }
            };

            // Bind click event to open the picker
            elm.bind('click', function() {
              $scope.reset();

              // Initialize the media picker available modes
              if (attrs.mediaPickerMode) {
                var modes = attrs.mediaPickerMode.split(',');

                $scope.picker.modes.enabled = [];

                for (var i = 0; i < modes.length; i++) {
                  $scope.picker.setMode(modes[i]);
                }
              }

              if (attrs.mediaPickerModeActive) {
                $scope.picker.enable(attrs.mediaPickerModeActive);
              }

              // Initialize the media picker available types
              if (attrs.mediaPickerType) {
                var types = attrs.mediaPickerType.split(',');

                $scope.picker.types.enabled = [];

                for (var i = 0; i < types.length; i++) {
                  $scope.picker.setType(types[i]);
                }
              }
              if (attrs.mediaPickerTarget && $scope.mediaPickerTarget) {
                var target = angular.copy($scope.mediaPickerTarget);

                if (!(target instanceof Array)) {
                  target = [ target ];
                }

                for (var i = 0; i < target.length; i++) {
                  $scope.selected.ids.push(target[i].pk_content);
                }

                $scope.selected.items = target;
              }

              var html = $scope.picker.render();
              var e    = $compile(html)($scope);

              $('body').append(e);

              // Make the page non-scrollable
              $('body').addClass('picker-open');

              $scope.loading = true;
              $scope.enhance = false;

              var route = {
                name:   'backend_ws_picker_mode',
                params: { mode: $scope.picker.modes.enabled }
              };

              // Get the parameters for the media picker
              http.put(route).then(function(response) {
                $scope.loading = false;
                $scope.picker.params = response.data;

                if ($scope.picker.isModeEnabled('explore')) {
                  $scope.explore();
                }
              });
            });
          }
        };
      }
    ])

    /**
     * @ngdoc controller
     * @name  MediaPickerCtrl
     *
     * @description
     *   Controller to handle media picker actions.
     *
     * @requires $rootScope
     * @requires $scope
     * @requires $timeout
     * @requires $window
     * @requires FileUploader
     * @requires DynamicImage
     * @requires http
     * @requires routing
     */
    .controller('MediaPickerCtrl', [
      '$rootScope', '$scope', '$timeout', '$window', 'FileUploader', 'DynamicImage', 'http', 'oqlEncoder', 'routing',
      function($rootScope, $scope, $timeout, $window, FileUploader, DynamicImage, http, oqlEncoder, routing) {
        /**
         * The array of contents.
         *
         * @type {Array}
         */
        $scope.contents = [];

        $scope.criteria = {
          epp: $scope.epp,
          in_litter: 0,
          orderBy: { created:  'desc' },
          page: $scope.page
        };

        /**
         * The number of elements per page.
         *
         * @type {integer}
         */
        $scope.epp = 20;

        /**
         * The current page.
         *
         * @type {integer}
         */
        $scope.page = 1;

        /**
         * The routing service.
         *
         * @type {Object}
         */
        $scope.routing = routing;

        /**
         * The list of selected contents.
         *
         * @type {Object}
         */
        $scope.selected = {
          items:        [],
          ids:          [],
          lastSelected: null
        };

        /**
         * Stores a timeout function.
         *
         * @type {Function}
         */
        $scope.tm = null;

        /**
         * The number of elements in contents.
         *
         * @type {integer}
         */
        $scope.total = 0;

        /**
         * The uploader object.
         *
         * @type {FileUploader}
         */
        $scope.uploader = null;

        /**
         * @function addItem
         * @memberof MediaPickerCtrl
         *
         * @description
         *   Adds a new item the the beginning of the array.
         *
         * @param Object item The item to add.
         */
        $scope.addItem = function(item) {
          if ($scope.picker.isTypeEnabled(item.content_type_name)) {
            $scope.contents.unshift(item);
          }
        };

        /**
         * @function close
         * @memberof MediaPickerCtrl
         *
         * @description
         *   Closes the media picker and launches the media picker close event.
         */
        $scope.close = function() {
          $rootScope.$broadcast('MediaPicker.close');
          $scope.picker.close();
        };

        /**
         * @function explore
         * @memberof MediaPickerCtrl
         *
         * @description
         *   Changes the picker to explore mode.
         */
        $scope.explore = function() {
          $scope.searchLoading = true;

          // Add a timeout to fix wrong epp calculation before full rendering
          $timeout(function() {
            var colWidth   = 135;
            var rowPadding = 20;
            var reduction  = 0.75;

            var h = $('.explore-panel .picker-panel-content').height();
            var w = $('.explore-panel .picker-panel-content').width();

            if (h === 100 && w === 100) {
              h = $('body').height() * reduction;
              w = $('body').width() * reduction;
            }

            // (Content height - padding) / (Item height + Item right margin)
            var rows = Math.ceil((h - rowPadding) / colWidth);

            // (Content width - padding) / (Item width + Item right margin)
            var cols = Math.floor((w - rowPadding) / colWidth);

            if (cols * rows > 0) {
              $scope.epp = cols * rows;
            }

            $scope.list(true);
          }, 100);
        };

        /**
         * @function insert
         * @memberof MediaPickerCtrl
         *
         * @description
         *   Launches the media picker insert event.
         */
        $scope.insert = function() {
          var items = $scope.selected.items;

          if ($scope.picker.selection.maxSize === 1) {
            items = items[0];
          }

          $rootScope.$broadcast(
            'MediaPicker.insert',
            {
              items: items,
              target: $scope.picker.target
            }
          );

          $scope.picker.close();
        };

        /**
         * @function isFlash
         * @memberof MediaPickerCtrl
         *
         * @description
         *   Checks if an item is a flash object.
         *
         * @param {Object} item The item to check.
         *
         * @return {Boolean} True if the item is a flash object. Otherwise, return
         *                   false.
         */
        $scope.isFlash = function(item) {
          return DynamicImage.isFlash(item);
        };

        /**
         * @function isIgnored
         * @memberof MediaPickerCtrl
         *
         * @description
         *   Checks if the given item is included in the list of ignored items.
         *
         * @param {Object} item The item to check.
         *
         * @return {boolean} True if the item is in the list of ignored items.
         *                   False otherwise.
         */
        $scope.isIgnored = function(item) {
          return $scope.mediaPickerIgnore &&
            $scope.mediaPickerIgnore.map(function(e) {
              return e.pk_content;
            }).indexOf(item.pk_content) !== -1;
        };

        /**
         * @function isSelectable
         * @memberof MediaPickerCtrl
         *
         * @description
         *   Checks if the item can be selected.
         *
         * @param {Object} item The item to check.
         *
         * @return {boolean} True if the item can be selected. False otherwise.
         */
        $scope.isSelectable = function(item) {
          return $scope.picker.selection.enabled &&
            (!$scope.mediaPickerIgnore ||
            $scope.mediaPickerIgnore.map(function(e) {
              return e.pk_content;
            }).indexOf(item.pk_content) === -1);
        };

        /**
         * @function isSelected
         * @memberof MediaPickerCtrl
         *
         * @description
         *   Checks if the given item is selected.
         *
         * @param {Object} item The item to check.
         *
         * @return {boolean} True if the given item is selected. Otherwise,
         *                 returns false.
         */
        $scope.isSelected = function(item) {
          return $scope.selected.ids.indexOf(item.pk_content) !== -1;
        };

        /**
         * @function list
         * @memberof MediaPickerCtrl
         *
         * @description
         *   Updates the array of contents.
         *
         * @param {boolean} reset Whether to reset the list or append more items.
         */
        $scope.list = function(reset) {
          $scope.loadingMore = true;

          if (reset) {
            $scope.page = 1;
            $scope.searchLoading = true;
          }

          $scope.criteria.epp               = $scope.epp;
          $scope.criteria.page              = $scope.page;
          $scope.criteria.content_type_name = $scope.picker.types.enabled;

          if ($scope.category) {
            $scope.criteria.category_id = $scope.category;
          }

          oqlEncoder.configure({
            placeholder: {
              title: '(title ~ "%[value]%" or description ~ "%[value]%")',
              created: '[key] ~ "%[value]%"'
            }
          });

          var oql = oqlEncoder.getOql($scope.criteria);

          var routes = {
            photo: {
              name: 'api_v1_backend_content_get_list',
              params:  { oql: oql }
            },
            video: {
              name: 'api_v1_backend_video_get_list',
              params: { oql: oql }
            }
          };

          var route = routes[$scope.criteria.content_type_name];

          http.get(route)
            .then(function(response) {
              $scope.loadingMore = false;

              if (reset) {
                $scope.contents      = response.data.items;
                $scope.total         = response.data.total;
                $scope.searchLoading = false;
              } else {
                $scope.contents = $scope.contents.concat(response.data.items);
              }

              $scope.total = response.data.total;

              if (response.data.hasOwnProperty('extra')) {
                $scope.extra = response.data.extra;
              }
            });
        };

        /**
         * @function reset
         * @memberof MediaPickerCtrl
         *
         * @description
         *   Resets the selected items.
         */
        $scope.reset = function() {
          $scope.contents = [];
          $scope.total = 0;
          $scope.page = 1;

          $scope.selected = {
            ids:          [],
            items:        [],
            lastSelected: null
          };

          $scope.uploader = new FileUploader({
            autoUpload: true,
            url:        routing.generate('api_v1_backend_photo_save_item')
          });

          // Filter files by extension
          $scope.uploader.filters.push({
            name: 'image',
            fn: function(item) {
              if (!item.type) {
                return false;
              }

              var type = item.type.slice(item.type.lastIndexOf('/') + 1);

              if (!type) {
                return false;
              }

              var types = [
                'bmp', 'flv', 'gif', 'ico', 'jpeg', 'jpg', 'ogm', 'pdf', 'png',
                'svg', 'svgz', 'swf', 'webp',
              ];

              return types.indexOf(type) !== -1;
            }
          });

          /**
           * Adds an event to change to explore mode on after adding a file.
           *
           * @param object fileItem The added item
           */
          $scope.uploader.onAfterAddingFile = function() {
            $scope.picker.enable('explore');
          };

          /**
           * Shows a messege when the file to upload is invalid.
           */
          $scope.uploader.onWhenAddingFileFailed = function() {
            $scope.invalid = true;
            $timeout(function() {
              $scope.invalid = false;
            }, 5000);
          };

          /**
           * Adds an event to update the list when a file upload is completed
           *
           * @param object fileItem The completed item.
           * @param object response The response content.
           * @param object code     The response code.
           */
          $scope.uploader.onCompleteItem = function(fileItem, response, code, headers) {
            $timeout(function() {
              $scope.uploader.removeFromQueue(fileItem);

              if (code !== 201) {
                $scope.uploadError = true;
                return;
              }
              var id = headers.location.substring(headers.location.lastIndexOf('/') + 1);
              var route = {
                name: 'api_v1_backend_photo_get_item',
                params:  { id: id }
              };

              http.get(route).then(function(response) {
                $scope.addItem(response.data.item);
                $scope.selected.lastSelected = response.data.item;

                if ($scope.picker.selection.enabled) {
                  $scope.selected.ids.push(response.data.item.pk_content);
                  $scope.selected.items.push(response.data.item);
                }
              });
            }, 500);
          };
        };

        /**
         * @function scroll
         * @memberof MediaPickerCtrl
         *
         * @description
         *   Requests the next page of the list when scrolling.
         */
        $scope.scroll = function() {
          if ($scope.total === $scope.contents.length) {
            return;
          }

          $scope.page = $scope.page + 1;
          $scope.list();
        };

        /**
         * @function saveDescription
         * @memberof MediaPickerCtrl
         *
         * @description
         *   Saves the last selected item description.
         */
        $scope.saveDescription = function() {
          $scope.saving = true;

          var data = { description: $scope.selected.lastSelected.description };
          var route  = {
            name:   'api_v1_backend_photo_patch_item',
            params: { id: $scope.selected.lastSelected.pk_content }
          };

          http.put(route, data).then(function() {
            $scope.saving = false;
            $scope.saved = true;

            $timeout(function() {
              $scope.saving = false;
              $scope.saved  = false;
            }, 2000);
          }, function() {
            $scope.saving = false;
            $scope.saved  = false;
            $scope.error  = true;

            $timeout(function() {
              $scope.error = false;
            }, 2000);
          });
        };

        /**
         * @function selectionMultiple
         * @memberof MediaPickerCtrl
         *
         * @description
         *   Selects multiple items from the last item selected to the given item.
         *
         * @param {Object} item The selected item.
         */
        $scope.selectionMultiple = function(item) {
          if ($scope.selected.items.length >= $scope.picker.selection.maxSize) {
            return;
          }

          var start = $scope.contents.indexOf($scope.selected.lastSelected);
          var end   = $scope.contents.indexOf(item);

          // Fix for right-to-left selection
          if (start > end) {
            var aux = start;

            start = end;
            end   = aux;
          }

          var itemsToInsert = end - start;

          if (itemsToInsert + $scope.selected.items.length >
              $scope.picker.selection.maxSize) {
            itemsToInsert =
              $scope.picker.selection.maxSize - $scope.selected.items.length;
          }

          var i = start;

          // Add all items between selected
          while (itemsToInsert > 0 && i < $scope.contents.length) {
            if (!$scope.isSelectable($scope.contents[i])) {
              itemsToInsert--;
              i++;
              continue;
            }

            if ($scope.selected.items.indexOf($scope.contents[i]) === -1) {
              $scope.selected.ids.push($scope.contents[i].pk_content);
              $scope.selected.items.push($scope.contents[i]);
              itemsToInsert--;
            }

            i++;
          }

          // Update last selected item
          $scope.selected.lastSelected = item;
        };

        /**
         * @function toggle
         * @memberof MediaPickerCtrl
         *
         * @description
         *   Selects one item or many items if shift is clicked.
         *
         * @param {Object} item  The selected item.
         * @param {Object} event The event object.
         */
        $scope.toggle = function(item, event) {
          if (!$scope.isSelectable(item)) {
            return;
          }

          // If shifKey
          if (event.shiftKey) {
            $scope.selectionMultiple(item);
            return;
          }

          // Update last selected item
          $scope.selected.lastSelected = item;

          if (event.ctrlKey) {
            return;
          }

          // Selection disabled
          if (!$scope.picker.selection.enabled) {
            return;
          }

          var index = $scope.selected.ids.indexOf(item.pk_content);

          // Remove element
          if (index !== -1) {
            $scope.selected.ids.splice(index, 1);
            $scope.selected.items.splice(index, 1);

            return;
          }

          // Empty selected if maxSize == 1 (toggle)
          if ($scope.picker.selection.maxSize === 1) {
            $scope.selected.ids   = [];
            $scope.selected.items = [];
          }

          // Add element
          if ($scope.selected.items.length < $scope.picker.selection.maxSize) {
            $scope.selected.ids.push(item.pk_content);
            $scope.selected.items.push(item);
          }
        };

        /**
         * @function enhanceAction
         * @memberof MediaPickerCtrl
         *
         * @description
         *   Open the photo editor for enhance the image
         *
         */
        $scope.enhanceAction = function() {
          $scope.enhance = !$scope.enhance;
          var photoEditor = new window.OnmPhotoEditor({
            container: 'photoEditor',
            image: $window.instanceMedia + $scope.selected.lastSelected.path,
            closeCallBack: $scope.uploadMediaImg,
            maximunSize: { width: 800, height: 600 }
          }, photoEditorTranslations);

          $('.picker-dialog').addClass('picker-photo-editor');
          photoEditor.init();
        };

        /**
         * @function uploadMediaImg
         * @memberof MediaPickerCtrl
         *
         * @description
         *   Close the photo editor and if it is needed upload the edited image
         *
         * @param {Object} image  The canvas image update in the photo editor
         */
        $scope.uploadMediaImg = function(image) {
          $scope.enhance = false;

          $('.picker-dialog').removeClass('picker-photo-editor');

          if (image === null) {
            $scope.$apply();
            return;
          }

          var route = { name: 'api_v1_backend_photo_save_item' };
          var body  = [];

          body[$scope.selected.lastSelected.name] = image;

          http.post(route, body).then(function() {
            $scope.list(true);
          }, function() {
            return false;
          });
        };

        /**
         * Refresh the list when the criteria changes.
         *
         * @param array nv The new values.
         * @param array ov The old values.
         */
        $scope.$watch('[category, criteria.created, criteria.title, title, from, to]', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if ($scope.tm) {
            $timeout.cancel($scope.tm);
          }

          $scope.tm = $timeout(function() {
            $scope.page = 1;
            $scope.list(true);
          }, 250);
        }, true);
      }
    ]);
})();
