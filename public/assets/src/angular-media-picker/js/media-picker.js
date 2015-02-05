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
                        <option value=\"\">[% picker.params.explore.all_months %]</option>\
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
                  <div class=\"media-item\" ng-repeat=\"content in contents\">\
                    <div class=\"checkbox\" ng-click=\"toggle(content)\">\
                      <input id=\"[% content.id %]\" ng-checked=\"isSelected(content)\" type=\"checkbox\">\
                      <label for=\"[% content.id %]\">\
                      </label>\
                        <dynamic-image class=\"img-thumbnail\" instance=\"" + instanceMedia + "\" path=\"[% content.path_file + '/' + content.name %]\" width=\"80\" transform=\"zoomcrop,120,120,center,center\" class=\"image-preview\"></dynamic-image>\
                    </div>\
                  </div>\
                </div>\
              </div>\
              <div class=\"media-picker-footer\"></div>\
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
              <i class=\"fa fa-folder\"></i>Media Library\
            </li>",

            upload: "<li ng-class=\"{ 'active': picker.isActive('upload') }\" ng-click=\"picker.enable('upload')\">\
              <i class=\"fa fa-upload\"></i>Upload\
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
              var sidebar = '';

              for (var i = 0; i < this.modes.available.length; i++) {
                sidebar += sidebarTpl[this.modes.available[i]];
                content += contentTpl[this.modes.available[i]];
              };

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
                // Reset html and body
                $('html, body').removeClass('media-picker-open');

                // Delete the current media picker
                $('.media-picker').remove();
                $('.media-picker-backdrop').remove();
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
      $scope.selected = [];

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
        return $scope.selected.indexOf(item.id) != -1;
      }

      $scope.toggle = function(item) {
        // Selection disabled
        if (!$scope.picker.selection.enabled) {
          console.log($scope.selected);
          return false;
        }

        // Remove element
        if ($scope.selected.indexOf(item.id) != -1) {
          $scope.selected.splice($scope.selected.indexOf(item.id), 1);
          console.log($scope.selected);
          return true;
        }

        // Add element
        if ($scope.selected.length < $scope.picker.selection.maxSize) {
          $scope.selected.push(item.id);
        }

        console.log($scope.selected);
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

        console.log(data);

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
    }
  ]);
