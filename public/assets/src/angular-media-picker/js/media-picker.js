/**
* onm.media-picker Module
*
* Creates a media picker modal to upload/insert contents.
*/
angular.module('onm.mediaPicker', ['angularFileUpload', 'onm.routing'])
  .directive('mediaPicker', ['$compile', '$http', 'routing',
    function($compile, $http, routing) {
      // Runs during compile
      return {
        controller: 'mediaPickerController',
        restrict: 'A', // E = Element, A = Attribute, C = Class, M = Comment
        scope: {},
        link: function($scope, elm, attrs) {
          /**
           * Template for the media picker content.
           *
           * @type string
           */
          var contentTpl = {
            explore: "<div class=\"media-picker-panel explore-panel\" ng-class=\"{ 'active': picker.isActive('explore') }\">\
              <div class=\"media-picker-panel-header clearfix\">\
                <h4 class=\"pull-left\">[% picker.params.explore.header %]</h4>\
              </div>\
              <div class=\"media-picker-panel-body\">\
                <div class=\"media-picker-panel-topbar\">\
                  <ul>\
                    <li>\
                      <select name=\"month\" ng-model=\"date\">\
                        <option value=\"\">[% picker.params.explore.allMonths %]</option>\
                        <optgroup label=\"[% year.name %]\" ng-repeat=\"year in picker.params.explore.dates\">\
                          <option value=\"[% month.value %]\" ng-repeat=\"month in year.months\">\
                            [% month.name %]\
                          </option>\
                        </optgroup>\
                      </select>\
                    </li>\
                    <li>\
                      <div class=\"input-group\">\
                        <span class=\"input-group-addon\">\
                          <i class=\"fa fa-search\"></i>\
                        </span>\
                        <input ng-model=\"title\" placeholder=\"[% picker.params.explore.search %]\" type=\"text\"/>\
                      </div>\
                    </li>\
                  </ul>\
                </div>\
                <div class=\"media-picker-panel-wrapper\">\
                  <div class=\"media-picker-panel-content\" when-scrolled=\"scroll()\">\
                    <div class=\"media-items\" ng-if=\"!loading\">\
                      <div class=\"media-item\" ng-repeat=\"item in uploader.queue\">\
                        <div class=\"img-thumbnail\">\
                          <i class=\"fa fa-picture-o fa-5x\"></i>\
                          <div class=\"progress\" style=\"margin-bottom: 0;\">\
                            <div class=\"progress-bar\" role=\"progressbar\" ng-style=\"{ 'width': item.progress + '%' }\"></div>\
                          </div>\
                        </div>\
                      </div>\
                      <div class=\"media-item[selectable]\"[selection] ng-repeat=\"content in contents track by $index\">\
                        <dynamic-image class=\"img-thumbnail\" instance=\""
                          + instanceMedia
                          + "\" ng-model=\"content\" width=\"80\" transform=\"zoomcrop,120,120,center,center\"></dynamic-image>\
                      </div>\
                    </div>\
                    <div class=\"media-items-loading\" ng-if=\"loading\">\
                      <i class=\"fa fa-circle-o-notch fa-spin fa-4x\"></i>\
                    </div>\
                  </div>\
                </div>\
                <div class=\"media-picker-panel-sidebar\">\
                  <div class=\"media-picker-panel-sidebar-header\">\
                    <h4>[% picker.params.explore.details %]</h4>\
                  </div>\
                  <div class=\"media-picker-panel-sidebar-body\" ng-if=\"selected.lastSelected\">\
                    <div class=\"media-thumbnail-wrapper\">\
                      <div class=\"media-dimensions-overlay\">\
                        <span class=\"media-dimensions-label\">\
                          [% selected.lastSelected.width %]x[% selected.lastSelected.height %]\
                        </span>\
                      </div>\
                      <dynamic-image class=\"img-thumbnail\" instance=\""
                        + instanceMedia
                        + "\" ng-model=\"selected.lastSelected\" transform=\"thumbnail,220,220\">\
                      </dynamic-image>\
                    </div>\
                    <ul class=\"media-information\">\
                      <li>\
                        <a ng-href=\"[% routing.generate('admin_photo_show', { id: selected.lastSelected.id}) %]\" target=\"_blank\">\
                          <strong>\
                            [% selected.lastSelected.name %]\
                            <i class=\"fa fa-edit\"></i>\
                          </strong>\
                        </a>\
                      </li>\
                      <li>[% selected.lastSelected.created | moment %]</li>\
                      <li>[% selected.lastSelected.size %] KB</li>\
                      <li><span class=\"v-seperate\"></span></li>\
                      <li>\
                        <div class=\"form-group\">\
                          <label for=\"description\">\
                            [% picker.params.explore.description %]\
                            <div class=\"pull-right\">\
                              <i class=\"fa\" ng-class=\"{ 'fa-circle-o-notch fa-spin': saving, 'fa-check text-success': saved, 'fa-times text-danger': error }\"></i>\
                            </div>\
                          </label>\
                          <textarea id=\"description\" ng-blur=\"saveDescription(selected.lastSelected.id)\" ng-model=\"selected.lastSelected.description\" cols=\"30\" rows=\"2\"></textarea>\
                        </div>\
                      </li>\
                    </ul>\
                  </div>\
                </div>\
              </div>\
              <div class=\"media-picker-panel-footer\" ng-class=\"{ 'collapsed': selected.items.length == 0 }\">\
                <ul class=\"pull-left\"  ng-if=\"selected.items.length > 0\">\
                  <li>\
                    <i class=\"fa fa-check fa-lg\" ng-click=\"selected.items = []\"></i>\
                  </li>\
                  <li>\
                    <span class=\"h-seperate\"></span>\
                  </li>\
                  <li>\
                    <h4>\
                      [% selected.items.length + ' ' + picker.params.explore.itemsSelected %]\
                    </h4>\
                  </li>\
                </ul>\
                <button class=\"btn btn-primary pull-right\" ng-click=\"insert()\">\
                  <i class=\"fa fa-plus\"></i>\
                  [% picker.params.explore.insert %]\
                </button>\
              </div>\
            </div>",

            upload: "<div class=\"media-picker-panel upload-panel\" ng-class=\"{ 'active': picker.isActive('upload') }\">\
              <div class=\"media-picker-panel-header clearfix\">\
                <h4 class=\"pull-left\">[% picker.params.upload.header %]</h4>\
              </div>\
              <div class=\"media-picker-panel-body\">\
                <div class=\"media-picker-panel-wrapper\">\
                  <div class=\"media-picker-panel-content\">\
                    <div class=\"drop-zone-text\">\
                      <h4>\
                        <div>\
                          <i class=\"fa fa-picture-o\" ng-if=\"picker.isTypeAllowed('photo')\"></i>\
                          <i class=\"fa fa-film\" ng-if=\"picker.isTypeAllowed('video')\"></i>\
                          <i class=\"fa fa-file-o\" ng-if=\"picker.isTypeAllowed('pdf')\"></i>\
                        </div>\
                        Drop files here to upload\
                      </h4>\
                      <h5>\
                        or click here\
                      </h5>\
                    </div>\
                  </div>\
                  <input type=\"file\" nv-file-select uploader=\"uploader\" multiple/>\
                </div>\
              </div>\
            </div>",
          };

          /**
           * Template for the media picker.
           *
           * @type string
           */
          var pickerTpl = "<div class=\"media-picker\">\
            <div class=\"media-picker-backdrop\"></div>\
            <div class=\"media-picker-dialog\" style=\"display: block;\">\
                <div class=\"media-picker-sidebar\">\
                  <ul>\
                    [sidebar]\
                  </ul>\
                </div>\
                <div class=\"media-picker-content\">\
                  <div class=\"media-picker-close\">\
                    <i class=\"fa fa-lg fa-times pull-right\"></i>\
                  </div>\
                  [content]\
                </div>\
              </div>\
            </div>\
          </div>";

          /**
           * Template for the media picker sidebar items.
           *
           * @type string
           */
          var sidebarTpl = {
            explore: "<li ng-class=\"{ 'active': picker.isActive('explore') }\" ng-click=\"picker.enable('explore'); explore()\">\
              <i class=\"fa fa-folder\"></i>[% picker.params.explore.menuItem %]\
            </li>",

            upload: "<li ng-class=\"{ 'active': picker.isActive('upload') }\" ng-click=\"picker.enable('upload'); upload()\">\
              <i class=\"fa fa-upload\"></i>[% picker.params.upload.menuItem %]\
            </li>"
          };

          /**
           * Default media picker configuration.
           *
           * @type Object
           */
          $scope.picker = {
            files: [ 'photo', 'video', 'pdf' ],
            modes: {
              active: attrs['mediaPickerMode'] ? attrs['mediaPickerMode'] : 'explore',
              available: [ 'upload', 'explore' ]
            },
            selection: {
              enabled: attrs['mediaPickerSelection'] == 'true' ? true : false,
              maxSize: attrs['mediaPickerMaxSize'] ? parseInt(attrs['mediaPickerMaxSize']) : 1,
            },
            src: {
              explore: attrs['mediaPickerExploreUrl'],
              upload: attrs['mediaPickerUploadUrl']
            },
            status: {
              editing:   false,
              loading:   false,
              uploading: false
            },

            /**
             * Closes the current media picker.
             */
            close: function() {
              // Reset html and body
              $('html, body').removeClass('media-picker-open');

              // Delete the current media picker
              $('.media-picker').remove();
              $('.media-picker-backdrop').remove();
            },

            /**
             * Checks if the given module is currently active.
             *
             * @param string mode The mode to check.
             *
             * @return boolean Returns true if the given mode is currently
             *                 active. Otherwise, returns false.
             */
            isActive: function(mode) {
              return this.modes.active == mode;
            },

            /**
             * Checks if a file type can be uploaded.
             *
             * @param string type The file type to check.
             *
             * @return boolean True if the given file type can be uploaded.
             *                 Otherwise, return false.
             */
            isTypeAllowed: function(type) {
              return this.files.indexOf(type) != -1;
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
             * Renders the media picker.
             */
            render: function() {
              var content = '';
              var picker  = pickerTpl;
              var selectable = '';
              var selection = '';
              var sidebar = '';

              for (var i = 0; i < this.modes.available.length; i++) {
                sidebar += sidebarTpl[this.modes.available[i]];
                content += contentTpl[this.modes.available[i]];
              };

              // Add selection actions
              if (this.selection.enabled) {
                selectable = ' selectable';
                selection  = "ng-class=\"{ 'selected': isSelected(content) }\" ng-click=\"toggle(content, $event)\"";
              }

              content = content.replace('[selectable]', selectable);
              content = content.replace('[selection]', selection);

              picker = picker.replace('[sidebar]', sidebar);
              picker = picker.replace('[content]', content);

              return picker;
            },
          };

          // Bind click event to open media-picker
          elm.bind('click', function() {
            $scope.reset();

            var url = routing.generate(
              'backend_ws_media_picker_mode',
              { mode: $scope.picker.modes.available }
            );

            // Get the parameters for the media picker
            $http.post(url).then(function(response) {
              $scope.picker.params = response.data;

              var html = $scope.picker.render();
              var e    = $compile(html)($scope);

              $('body').append(e);

              // Make the page non-scrollable
              $('html, body').addClass('media-picker-open');

              // Hide and destroy the media picker
              e.find('.media-picker-close').bind('click', function() {
                $scope.picker.close();
              });

              if ($scope.picker.modes.active == 'explore') {
                $scope.explore();
              } else {
                $scope.upload();
              }
            })
          })
        }
      };
    }
  ])
  .controller('mediaPickerController', ['$http', '$rootScope', '$scope', '$timeout', 'FileUploader', 'itemService', 'routing',
    function($http, $rootScope, $scope, $timeout, FileUploader, itemService, routing) {
      /**
       * The array of contents.
       *
       * @type array
       */
      $scope.contents = [];

      /**
       * The number of elements per page.
       *
       * @type integer
       */
      $scope.epp = 20;

      /**
       * The current page.
       *
       * @type integer
       */
      $scope.page = 1;

      /**
       * The routing service.
       *
       * @type object
       */
      $scope.routing = routing;

      /**
       * The list of selected contents.
       *
       * @type object
       */
      $scope.selected = {
        items:        [],
        lastSelected: null
      };

      /**
       * The number of elements in contents.
       *
       * @type integer
       */
      $scope.total;

      /**
       * The uploader object.
       *
       * @type FileUploader
       */
      $scope.uploader;

      /**
       * Adds a new item the the beginning of the array.
       *
       * @param Object item The item to add.
       */
      $scope.addItem = function(item) {
        $scope.contents.unshift(item);
      };

      /**
       * Changes the picker to explore mode.
       */
      $scope.explore = function() {
        // Add a timeout to fix wrong epp calculation before full rendering
        $timeout(function() {
          var h = $('.explore-panel .media-picker-panel-content').outerHeight();
          var w = $('.explore-panel .media-picker-panel-content').outerWidth();

          // (Content height - padding) / (Item height + Item right margin)
          var rows = Math.ceil((h - 20) / 135);

          // (Content width - padding) / (Item width + Item right margin)
          var cols = Math.floor((w - 20) / 135);

          if (cols * rows > 0) {
            $scope.epp = cols * rows;
          }

          $scope.list();
        }, 100);
      };

      /**
       * Launches the media picker insert event.
       */
      $scope.insert = function() {
        $rootScope.$broadcast('media-picker-insert', $scope.selected.items);
        $scope.picker.close();
      };

      /**
       * Checks if the given item is selected.
       *
       * @param object item The item to check.
       *
       * @return boolean True if the given item is selected. Otherwise,
       *                 returns false.
       */
      $scope.isSelected = function(item) {
        return $scope.selected.items.indexOf(item) != -1;
      };

      /**
       * Updates the array of contents.
       */
      $scope.list = function (reset) {
        if (reset) {
          $scope.loading = true;
        }

        var data = {
            epp:        $scope.epp,
            page:       $scope.page,
            sort_by:    'created',
            sort_order: 'desc',
        }

        if ($scope.title) {
          data.title = $scope.title;
        }

        if ($scope.date) {
          data.date = $scope.date;
        }

        var url = routing.generate('backend_ws_media_picker_list', data);

        $http.get(url).then(function(response) {
          if (reset) {
            $scope.contents = response.data.results;
            $scope.loading = false;
          } else {
            $scope.contents = $scope.contents.concat(response.data.results);
          }

          $scope.total = response.data.total

          if (response.data.hasOwnProperty('extra')) {
            $scope.extra = response.data.extra;
          };
        });
      };

      /**
       * Resets the selected items.
       */
      $scope.reset = function() {
        $scope.contents = [];
        $scope.total = 0;
        $scope.page = 1;

        $scope.selected = {
          items:        [],
          lastSelected: null
        };

        $scope.uploader = new FileUploader({
            url:               $scope.picker.src.upload,
            autoUpload:        true,
        });

        /**
         * Adds an event to change to explore mode on after adding a file.
         *
         * @param object fileItem The added item
         */
        $scope.uploader.onAfterAddingFile = function(fileItem) {
          $scope.picker.enable('explore');
        };

        /**
         * Adds an event to update the list when a file upload is completed
         *
         * @param object fileItem The completed item.
         * @param object response The response content.
         * @param string status   The response status.
         * @param object headers  The response headers.
         */
        $scope.uploader.onCompleteItem = function(fileItem, response, status, headers) {
          $timeout(function() {
            $scope.uploader.removeFromQueue(fileItem);
            $scope.addItem(response);
          }, 500);
        }
      };

      /**
       * Requests the next page of the list when scrolling.
       */
      $scope.scroll = function() {
        if ($scope.total == $scope.contents.length) {
          return false;
        }

        $scope.page = $scope.page + 1;
        $scope.list();
      };

      /**
       * Saves the last selected item description.
       */
      $scope.saveDescription = function() {
        $scope.saving = true;

        var data = { description: $scope.selected.lastSelected.description };
        var url  = routing.generate(
          'backend_ws_media_picker_save_description',
          { id: $scope.selected.lastSelected.id }
        );

        $http.post(url, data).then(function(response) {
          $scope.saving = false;
          $scope.saved = true;

          if (response.status == 200) {
            $timeout(function() {
              $scope.saved = false;
            }, 2000);

            return true;
          }

          if (response.status != 200) {
            $scope.saved = false;
            $scope.error = true;

            $timeout(function() {
              $scope.error = false;
            }, 2000);

            return false;
          }
        });

      };

      /**
       * Selects multiple items from the last item selected to the given item.
       *
       * @param object item The selected item.
       */
      $scope.selectionMultiple = function(item) {
        if ($scope.selected.items.length >= $scope.picker.selection.maxSize) {
          return false;
        }

        var start = $scope.contents.indexOf($scope.selected.lastSelected);
        var end   = $scope.contents.indexOf(item);

        // Fix for right-to-left selection
        if (start > end) {
          var aux = start;
          start   = end;
          end     = aux;
        }

        var itemsToInsert = end - start;
        if (itemsToInsert + $scope.selected.items.length > $scope.picker.selection.maxSize) {
          itemsToInsert = $scope.picker.selection.maxSize - $scope.selected.items.length;
        }

        // Add all items between selected
        var i = start;
        while (itemsToInsert > 0 && i < $scope.contents.length) {
          if ($scope.selected.items.indexOf($scope.contents[i]) == -1) {
            $scope.selected.items.push($scope.contents[i]);
            itemsToInsert--;
          }

          i++;
        }

        // Update last selected item
        $scope.selected.lastSelected = item;
      };

      /**
       * Selects one item or many items if shift is clicked.
       *
       * @param object item  The selected item.
       * @param object event The event object.
       */
      $scope.toggle = function(item, event) {
        // If shifKey
        if (event.shiftKey) {
          return $scope.selectionMultiple(item);
        }

        // Update last selected item
        $scope.selected.lastSelected = item;

        if (event.ctrlKey) {
          return false;
        }

        // Selection disabled
        if (!$scope.picker.selection.enabled) {
          return false;
        }

        // Remove element
        if ($scope.selected.items.indexOf(item) != -1) {
          $scope.selected.items.splice($scope.selected.items.indexOf(item), 1);
          return true;
        }

        // Empty selected if maxSize == 1 (toggle)
        if ($scope.picker.selection.maxSize == 1) {
          $scope.selected.items = [];
        }

        // Add element
        if ($scope.selected.items.length < $scope.picker.selection.maxSize) {
          $scope.selected.items.push(item);
        }
      };

      /**
       * Refresh the list when the criteria changes.
       *
       * @param array nv The new values.
       * @param array ov The old values.
       */
      var search;
      $scope.$watch('[date,title]', function(nv, ov) {
        if (nv == ov) {
          return false;
        }

        if (search) {
          $timeout.cancel(search);
        }

        search = $timeout(function() {
          $scope.page = 1;
          $scope.list(true);
        }, 250);
      }, true);
    }
  ]);
