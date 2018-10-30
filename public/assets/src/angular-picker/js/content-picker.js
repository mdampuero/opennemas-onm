(function() {
  'use strict';

  angular.module('onm.picker')

    /**
     * @ngdoc directive
     * @name  contentPicker
     *
     * @requires $compile
     * @requires $http
     * @requires routing
     *
     * @description
     *   Directive to create and display the media picker modal window.
     */
    .directive('contentPicker', [
      '$compile', '$http', 'routing',
      function($compile, $http, routing) {
        // Runs during compile
        return {
          controller: 'contentPickerCtrl',
          restrict: 'A',
          scope: {
            contentPickerTarget: '='
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
                        '<div class="input-group">' +
                          '<span class="input-group-addon">' +
                            '<i class="fa fa-search"></i>' +
                          '</span>' +
                          '<input ng-model="$parent.title" placeholder="[% picker.params.explore.search %]" type="text"/>' +
                        '</div>' +
                      '</li>' +
                      '<li>' +
                        '<ui-select class=" select2-border w-200" name="content-type" theme="select2" ng-model="criteria.contentType">' +
                          '<ui-select-match>' +
                            '[% $select.selected.title %]' +
                          '</ui-select-match>' +
                          '<ui-select-choices repeat="type.name as type in picker.params.explore.contentTypes | filter: { title: $select.search }">' +
                            '<div ng-bind-html="type.title | highlight: $select.search"></div>' +
                          '</ui-select-choices>' +
                        '</ui-select>' +
                      '</li>' +
                      '<li ng-if="criteria.contentType != \'contents-in-frontpage\' && criteria.contentType != \'photo\'">' +
                        '<ui-select class=" select2-border" name="category" theme="select2" ng-model="criteria.category">' +
                          '<ui-select-match>' +
                            '[% $select.selected.title %]' +
                          '</ui-select-match>' +
                          '<ui-select-choices group-by="groupCategories" repeat="category.pk_content_category as category in picker.params.explore.categories | filter: { title: $select.search }">' +
                            '<div ng-bind-html="category.title | highlight: $select.search"></div>' +
                          '</ui-select-choices>' +
                        '</ui-select>' +
                      '</li>' +
                    '</ul>' +
                  '</div>' +
                  '<div class="picker-panel-wrapper">' +
                    '<div class="picker-panel-content" when-scrolled="scroll()">' +
                      '<div class="items" ng-if="!searchLoading">' +
                        '<div class="list-item [selectable]"[selection] ng-repeat="content in contents track by $index">' +
                          '<div>' +
                            '[% content.content_type_l10n_name %] - [% content.title %]' +
                          '</div>' +
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
                      '<h4>[% picker.params.explore.itemDetails %]</h4>' +
                    '</div>' +
                    '<div class="picker-panel-sidebar-body" ng-if="selected.lastSelected">' +
                      '<ul class="media-information">' +
                        '<li>' +
                          '<strong>' +
                            '[% selected.lastSelected.title %]' +
                          '</strong>' +
                        '</li>' +
                        '<li ng-show="selected.lastSelected.category_name"><strong>[% picker.params.explore.category %]:</strong> [% selected.lastSelected.category_name %]</li>' +
                        '<li ng-show="selected.lastSelected.created"><strong>[% picker.params.explore.created %]:</strong> [% selected.lastSelected.created | moment %]</li>' +
                        '<li ng-show="selected.lastSelected.description">' +
                          '<div><strong>[% picker.params.explore.description %]</strong></div>' +
                          '[% selected.lastSelected.description %]' +
                        '</li>' +
                      '</ul>' +
                    '</div>' +
                  '</div>' +
                '</div>' +
                '<div class="picker-panel-footer" ng-class="{ \'collapsed\': selected.items.length == 0 }">' +
                  '<ul class="pull-left"  ng-if="selected.items.length > 0">' +
                    '<li>' +
                      '<i class="fa fa-check fa-lg" ng-click="selected.ids = [];selected.items = []"></i>' +
                    '</li>' +
                    '<li>' +
                      '<span class="h-seperate"></span>' +
                    '</li>' +
                    '<li>' +
                      '<h4>' +
                        '[% selected.items.length %]' +
                        '<span class="hidden-xs">[% picker.params.explore.itemsSelected %]</span>' +
                      '</h4>' +
                    '</li>' +
                  '</ul>' +
                  '<button class="btn btn-primary pull-right" ng-click="insert()">' +
                    '<i class="fa fa-plus"></i>' +
                    '[% picker.params.explore.insert %]' +
                  '</button>' +
                '</div>' +
              '</div>'
            };

            /**
             * Template for the media picker.
             *
             * @type string
             */
            var pickerTpl = '<div class="picker">' +
              '<div class="picker-backdrop"></div>' +
              '<div class="picker-dialog">' +
                  '<div class="picker-close" ng-click="close()">' +
                    '<i class="fa fa-lg fa-times pull-right"></i>' +
                  '</div>' +
                  '<div class="picker-loading" ng-if="loading">' +
                    '<i class="fa fa-circle-o-notch fa-spin fa-4x"></i>' +
                  '</div>' +
                  '<div class="picker-content" ng-if="!loading">' +
                    '[content]' +
                  '</div>' +
                '</div>' +
              '</div>' +
            '</div>';

            /**
             * Default media picker configuration.
             *
             * @type Object
             */
            $scope.picker = {
              modes: {
                active:    'explore',
                available: [ 'explore' ],
                enabled:   [ 'explore' ]
              },
              section: attrs.contentPickerSection ?
                attrs.contentPickerSection : 'default',
              selection: {
                enabled: attrs.contentPickerSelection === 'true',
                maxSize: attrs.contentPickerMaxSize ?
                  parseInt(attrs.contentPickerMaxSize) : 1
              },
              status: {
                editing:   false,
                loading:   false,
                uploading: false
              },
              target: attrs.contentPickerTarget,
              types: {
                enabled:   [ ],
                available: [
                  'album', 'article', 'attachment', 'opinion', 'photo', 'poll',
                  'video', 'special'
                ]
              },

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
                return this.modes.enabled.indexOf(mode);
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
                var content = contentTpl.explore;
                var picker  = pickerTpl;
                var selectable = '';
                var selection = '';

                // Add selection actions
                if (this.selection.enabled) {
                  selectable = ' selectable';
                  selection  = 'ng-class="{ \'selected\': isSelected(content) }" ng-click="toggle(content, $event)"';
                }

                content = content.replace(/\[selectable\]/g, selectable);
                content = content.replace(/\[selection\]/g, selection);
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

            // Bind click event to open picker
            elm.bind('click', function() {
              $scope.reset();

              // Initialize the media picker available modes
              if (attrs.contentPickerMode) {
                var modes = attrs.contentPickerMode.split(',');

                $scope.picker.modes.enabled = [];

                for (var i = 0; i < modes.length; i++) {
                  $scope.picker.setMode(modes[i]);
                }
              }

              if (attrs.contentPickerModeActive) {
                $scope.picker.enable(attrs.contentPickerModeActive);
              }

              // Initialize the media picker available types
              if (attrs.contentPickerType) {
                var types = attrs.contentPickerType.split(',');

                $scope.picker.types.enabled = [];

                for (var j = 0; j < types.length; j++) {
                  $scope.picker.setType(types[j]);
                }
              }

              if (attrs.contentPickerTarget && $scope.contentPickerTarget) {
                var target = $scope.contentPickerTarget;

                if (!(target instanceof Array)) {
                  target = [ target ];
                }

                for (var z = 0; z < target.length; z++) {
                  $scope.selected.ids.push(target[z].id);
                }

                $scope.selected.items = target;
              }

              var html = $scope.picker.render();
              var e    = $compile(html)($scope);

              $('body').append(e);

              // Make the page non-scrollable
              $('body').addClass('picker-open');

              $scope.loading = true;

              var url = routing.generate(
                'backend_ws_picker_mode',
                { mode: $scope.picker.modes.enabled }
              );

              if ($scope.picker.section === 'newsletter') {
                $scope.criteria.contentType = 'contents-in-frontpage';
              }

              // Get the parameters for the media picker
              $http.post(url).then(function(response) {
                $scope.loading = false;
                $scope.picker.params = response.data;

                $scope.picker.params.explore.contentTypes =
                  $scope.picker.params.explore.contentTypes.filter(function(e) {
                    return e.name === null ||
                      e.name === 'contents-in-frontpage' ||
                      $scope.picker.types.enabled.indexOf(e.name) !== -1;
                  });

                $scope.explore();
              });
            });
          }
        };
      }
    ])

    /**
     * @ngdoc controller
     * @name  contentPickerCtrl
     *
     * @description
     *   Controller to handle media picker actions.
     *
     * @requires $http
     * @requires $rootScope
     * @requires $scope
     * @requires $timeout
     * @requires itemService
     * @requires routing
     */
    .controller('contentPickerCtrl', [
      '$http', '$rootScope', '$scope', '$timeout', 'itemService', 'routing',
      function($http, $rootScope, $scope, $timeout, itemService, routing) {
        /**
         * The array of contents.
         *
         * @type {Array}
         */
        $scope.contents = [];

        /**
         * The criteria object.
         *
         * @type {Object}
         */
        $scope.criteria = {};

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
         * @type {Integer}
         */
        $scope.total = 0;

        /**
         * The uploader object.
         *
         * @type {FileUploader}
         */
        $scope.uploader = null;

        /**
         * @function close
         * @memberof contentPickerCtrl
         *
         * @description
         *   Closes the media picker and launches the media picker close event.
         */
        $scope.close = function() {
          $rootScope.$broadcast('contentPicker.close');
          $scope.picker.close();
        };

        /**
         * @function explore
         * @memberof contentPickerCtrl
         *
         * @description
         *   Changes the picker to explore mode.
         */
        $scope.explore = function() {
          $timeout(function() {
            $scope.list();
          }, 100);
        };

        /**
         * @function groupCategories
         * @memberOf ContentPickerCtrl
         *
         * @description
         *   Groups categories in the ui-select.
         *
         * @param {Object} item The category to group.
         *
         * @return {String} The group name.
         */
        $scope.groupCategories = function(item) {
          var category = $scope.picker.params.explore.categories
            .filter(function(e) {
              return e.pk_content_category === item.fk_content_category;
            });

          if (category.length > 0 && category[0].pk_content_category) {
            return category[0].title;
          }

          return '';
        };

        /**
         * @function insert
         * @memberof contentPickerCtrl
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
            'ContentPicker.insert',
            {
              items: items,
              target: $scope.picker.target
            }
          );

          $scope.picker.close();
        };

        /**
         * @function isSelected
         * @memberof contentPickerCtrl
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
          return $scope.selected.ids.indexOf(item.id) !== -1;
        };

        /**
         * @function list
         * @memberof contentPickerCtrl
         *
         * @description
         *   Updates the array of contents.
         *
         * @param {boolean} reset Whether to reset the list or append more items.
         */
        $scope.list = function(reset) {
          $scope.loadingMore = true;

          if (reset) {
            $scope.searchLoading = true;
          }

          var data = {
            category: $scope.criteria.category ?
              $scope.criteria.category : null,
            content_type_name: $scope.criteria.contentType ?
              [ $scope.criteria.contentType ] : $scope.picker.types.enabled,
            epp:        $scope.epp,
            page:       $scope.page,
            sort_by:    'created',
            sort_order: 'desc'
          };

          if ($scope.title) {
            data.title = $scope.title;
          }

          if ($scope.date) {
            data.date = $scope.date;
          }

          var url = routing.generate('backend_ws_picker_list', data);

          $http.get(url).then(function(response) {
            $scope.loadingMore = false;

            if (reset) {
              $scope.contents      = response.data.results;
              $scope.total         = response.data.total;
              $scope.searchLoading = false;
            } else {
              $scope.contents = $scope.contents.concat(response.data.results);
            }

            $scope.total = response.data.total;

            if (response.data.hasOwnProperty('extra')) {
              $scope.extra = response.data.extra;
            }
          });
        };

        /**
         * @function reset
         * @memberof contentPickerCtrl
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
        };

        /**
         * @function scroll
         * @memberof contentPickerCtrl
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
         * @function selectionMultiple
         * @memberof contentPickerCtrl
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

          // Add all items between selected
          var i = start;

          while (itemsToInsert > 0 && i < $scope.contents.length) {
            if ($scope.selected.items.indexOf($scope.contents[i]) === -1) {
              $scope.selected.ids.push($scope.contents[i].id);
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
         * @memberof contentPickerCtrl
         *
         * @description
         *   Selects one item or many items if shift is clicked.
         *
         * @param {Object} item  The selected item.
         * @param {Object} event The event object.
         */
        $scope.toggle = function(item, event) {
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

          // Remove element
          var index = $scope.selected.ids.indexOf(item.id);

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
            $scope.selected.ids.push(item.id);
            $scope.selected.items.push(item);
          }
        };

        /**
         * Refresh the list when the criteria changes.
         *
         * @param array nv The new values.
         * @param array ov The old values.
         */
        $scope.$watch('[date,title,criteria]', function(nv, ov) {
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
