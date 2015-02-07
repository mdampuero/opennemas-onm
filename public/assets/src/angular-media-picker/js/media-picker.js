/**
* onm.media-picker Module
*
* Creates a media picker modal to upload/insert contents.
*/
angular.module('onm.mediaPicker', ['onm.routing'])
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
            explore: "<div class=\"media-picker-panel\" ng-class=\"{ 'active': picker.isActive('explore') }\">\
              <div class=\"media-picker-header clearfix\">\
                <h4 class=\"pull-left\">[% picker.params.explore.header %]</h4>\
                <i class=\"fa fa-lg fa-times media-picker-close pull-right\"></i>\
              </div>\
              <div class=\"media-picker-body\">\
                <div class=\"media-picker-bar\">\
                  <ul>\
                    <li>\
                      <select name=\"month\" ng-model=\"filters.date\">\
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
                        <input ng-model=\"filters.search\" placeholder=\"[% picker.params.explore.search %]\" type=\"text\"/>\
                      </div>\
                    </li>\
                  </ul>\
                </div>\
                <div class=\"media-picker-wrapper\">\
                  <div class=\"media-items\">\
                    <div class=\"media-item[selectable]\"[selection] ng-repeat=\"content in contents\">\
                      <dynamic-image class=\"img-thumbnail\" instance=\""
                        + instanceMedia
                        + "\" path=\"[% content.path_file + '/' + content.name %]\" width=\"80\" transform=\"zoomcrop,120,120,center,center\"></dynamic-image>\
                    </div>\
                  </div>\
                </div>\
                <div class=\"media-information-sidebar\">\
                  <h4>[% picker.params.explore.details %]</h4>\
                  <div class=\"media-thumbnail-wrapper\" ng-if=\"selected.lastSelected\">\
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
                  <div class=\"media-information\">\
                    <ul>\
                      <li>[% selected.lastSelected.name %]</li>\
                      <li>[% selected.lastSelected.created | moment %]</li>\
                      <li>[% selected.lastSelected.size %] KB</li>\
                    </ul>\
                  </div>\
                </div>\
              </div>\
              <div class=\"media-picker-footer\" ng-class=\"{ 'collapsed': selected.items.length == 0 }\">\
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
                <button class=\"btn btn-primary pull-right\">\
                  <i class=\"fa fa-plus\"></i>\
                  [% picker.params.explore.insert %]\
                </button>\
              </div>\
            </div>",

            upload: "<div class=\"media-picker-panel\" ng-class=\"{ 'active': picker.isActive('upload') }\">\
              <div class=\"media-picker-header clearfix\">\
                <h4 class=\"pull-left\">[% picker.params.upload.header %]</h4>\
                <i class=\"fa fa-lg fa-times media-picker-close pull-right\"></i>\
              </div>\
              <div class=\"media-picker-body\">\
                <div class=\"media-picker-bar\">\
                  <ul>\
                    <li>\
                      <button class=\"btn btn-default\">\
                        <i class=\"fa fa-plus\"></i>\
                        [% picker.params.upload.add %]\
                      </button>\
                    </li>\
                  </ul>\
                </div>\
              </div>\
              <div class=\"media-picker-footer\"></div>\
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

            upload: "<li ng-class=\"{ 'active': picker.isActive('upload') }\" ng-click=\"picker.enable('upload')\">\
              <i class=\"fa fa-upload\"></i>[% picker.params.upload.menuItem %]\
            </li>"
          };

          /**
           * Default media picker configuration.
           *
           * @type Object
           */
          $scope.picker = {
            modes: {
              active: 'upload',
              available: [ 'upload', 'explore' ]
            },
            selection: {
              enabled: attrs['selection'] == 'true' ? true : false,
              maxSize: attrs['maxSize'] ? parseInt(attrs['maxSize']) : 1,
            },
            src: attrs['mediaPicker'],
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
             * Enables the given mode.
             *
             * @param string mode The mode to enable.
             */
            enable: function(mode) {
              this.modes.active = mode;
            },

            renderPicker: function() {
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
                selection  = "ng-class=\"{ 'selected': isSelected(content) }\" ng-click=\"toggle($event, content)\"";
              }

              content = content.replace('[selectable]', selectable);
              content = content.replace('[selection]', selection);

              picker = picker.replace('[sidebar]', sidebar);
              picker = picker.replace('[content]', content);

              return picker;
            },

            render: function() {
              return this.renderPicker();
            }
          };

          elm.bind('click', function() {
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
            })
          })
        }
      };
    }
  ])
  .controller('mediaPickerController', ['$http', '$scope', 'itemService', 'routing',
    function($http, $scope, itemService, routing) {
      /**
       * The array of contents.
       *
       * @type array
       */
      $scope.contents = [];

      /**
       * The search criteria.
       *
       * @type object
       */
      $scope.criteria = {
        content_type_name: [ { value: 'photo' } ]
      }

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
       * The list of selected contents.
       *
       * @type Array
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
      $scope.total = 0;

      /**
       * Changes the picker to explore mode.
       */
      $scope.explore = function() {
        $scope.list();
      };

      $scope.isSelected = function(item) {
        return $scope.selected.items.indexOf(item.id) != -1;
      }

      $scope.toggle = function(event, item) {
        // If shifKey
        if (event.shiftKey) {
          return $scope.selectionMultiple(item);
        }

        // Update last selected item
        $scope.selected.lastSelected = item;

        // Selection disabled
        if (!$scope.picker.selection.enabled) {
          return false;
        }

        // Remove element
        if ($scope.selected.items.indexOf(item.id) != -1) {
          $scope.selected.items.splice($scope.selected.items.indexOf(item.id), 1);
          return true;
        }

        // Empty selected if maxSize == 1 (toggle)
        if ($scope.picker.selection.maxSize == 1) {
          $scope.selected.items = [];
        }

        // Add element
        if ($scope.selected.items.length < $scope.picker.selection.maxSize) {
          $scope.selected.items.push(item.id);
        }
      }

      /**
       * Updates the array of contents.
       */
      $scope.list = function () {
        $scope.loading = 1;

        var data = {
            elements_per_page: $scope.epp,
            page:              $scope.page,
            sort_by:           'created',
            sort_order:        'desc',
            search:            itemService.cleanFilters($scope.criteria)
        }

        var url = routing.generate(
          'backend_ws_contents_list',
          { contentType: 'photo'}
        );

        $http.post(url, data).then(function(response) {
          $scope.contents = response.data.results;
          $scope.total    = response.data.total

          if (response.data.hasOwnProperty('extra')) {
            $scope.extra = response.data.extra;
          };

          $scope.loading = 0;
        });
      }

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
          if ($scope.selected.items.indexOf($scope.contents[i].id) == -1) {
            $scope.selected.items.push($scope.contents[i].id);
            itemsToInsert--;
          }

          i++;
        }

        // Update last selected item
        $scope.selected.lastSelected = item;
      }
    }
  ]);
