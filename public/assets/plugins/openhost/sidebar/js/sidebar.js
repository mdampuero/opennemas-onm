/**
 * onm.sidebar Module
 *
 * Description
 */
angular.module('onm.sidebar', [])
  .directive('sidebar', ['$compile', '$http', '$location', '$rootScope', '$window', 'history', 'routing',
    function($compile, $http, $location, $rootScope, $window, history, routing){
      return {
        restrict: 'E',
        scope: {
          ngModel: '='
        },
        link: function($scope, elm, attrs) {
          /**
           * Default template for the sidebar.
           *
           * @type string
           */
          var defaultSidebarTpl = '<div class="sidebar">\
            <div class="spinner-wrapper">\
              <div class="spinner">\
                <i class="fa fa-circle-o-notch fa-3x fa-spin"></i>\
              </div>\
            </div>\
          </div>';

          /**
           * Template for the sidebar footer.
           *
           * @type string
           */
          var footerTpl = '<div class="sidebar-footer-widget">\
            <ul>\
              <li class="profile-info">\
                <a ng-href="' + routing.ngGenerate('manager_user_show', { id: 'me' }) + '">\
                  <div class="profile-pic">\
                    <img class="gravatar" email="[% $parent.user.email %]" image="1" size="32" width=32 height=32 >\
                  </div>\
                  <div class="username">\
                    [% $parent.user.name %]\
                  </div>\
                </a>\
                <div class="logout" ng-click="$parent.logout();">\
                  <i class="fa fa-power-off"></i>\
                </div>\
              </li>\
            </ul>\
          </div>';

          /**
           * Template for a sidebar item.
           *
           * @type string
           */
          var itemTpl = '<li ng-class="{\'active open\': [urls]}"[click]>\
            <a ng-href="#">\
              <i class="fa[icon-class]"[spinner]></i>\
              <span class="title">[name]</span>\
              [arrow]\
            </a>\
            [submenu]\
          </li>';

          /**
           * Template for the sidebar.
           *
           * @type string
           */
          var sidebarTpl = '<div class="[position][inverted]" ng-class="{ \'collapsed\': ngModel.isCollapsed() }">\
            <div class="[class]" id="[id]"[swipeable]>\
              <div class="overlay"></div>\
              <scrollable>\
                <div class="sidebar-wrapper">\
                  <ul>\
                    [items]\
                  </ul>\
                </div>\
              </scrollable>\
              [footer]\
            </div>\
            [border]\
          </div>';

          if (!attrs['src']) {
            return;
          }

          var dft = $compile(defaultSidebarTpl)($scope);
          elm.replaceWith(dft);

          var url = routing.generate(attrs['src']);
          return $http.get(url).then(function(response) {
            /**
             * Sidebar definition
             *
             * @type Object
             */
            $scope.ngModel = {
              changing:  {},
              collapsed: false,
              data:      response.data,
              footer:    attrs['footer'] == 'true' ? attrs['footer'] : false,
              forced:    false,
              id:        attrs['id'] ? attrs['id'] : null,
              inverted:  attrs['inverted'] == 'true' ? attrs['inverted'] : false,
              class:     attrs['class'] ? attrs['class'] : 'sidebar',
              pinnable:  attrs['pinnable'] == 'true' ? attrs['pinnable'] : true,
              pinned:    false,
              position:  attrs['position'] ? attrs['position'] : 'left',
              swipeable: attrs['swipeable'] == 'true' ? attrs['swipeable'] : true,
              threshold: 992, // Minimum window width for a static sidebar

              /**
               * Resets the changing status for the sidebar.
               */
              changed: function() {
                this.changing = {};
              },

              /**
               * Checks the sidebar status basing on the current window width.
               */
              check: function() {
                this.forced    = false;
                this.collapsed = this.pinned;

                if ($window.innerWidth < this.threshold) {
                  this.forced    = true;
                  this.collapsed = true;
                }
              },

              /**
               * Collapses the sidebar.
               */
              close: function() {
                this.collapsed = true;
              },

              /**
               * Returns the default HTML for sidebar.
               *
               * @return string The default HTML code for the sidebar.
               */
              default: function() {
                return defaultSidebarTpl;
              },

              /**
               * Updates the model for the current sidebar.
               *
               * @param object sidebar The sidebar values.
               */
              init: function(route) {
                return $http.get(routing.generate(route)).then(function(response) {
                  angular.extend(sidebar.model, response.data);
                });
              },

              /**
               * Checks if an URL is active.
               *
               * @param string url The URL to check.
               *
               * @return boolean True if the URL is active. Otherwise, return false.
               */
              isActive: function(url) {
                return $location.path() == url.replace('#', '');
              },

              /**
               * Checks if an item is active (waiting for the response from server)
               * given its route.
               *
               * @param string route The route name.
               *
               * @return boolean True if the item active. Otherwise, returns false.
               */
              isChanging: function(route) {
                return this.changing && this.changing[route];
              },

              /**
               * Returns the current sidebar status.
               *
               * @return boolean True if the sidebar is collapsed. Otherwise, returns
               *                 false
               */
              isCollapsed: function() {
                return this.collapsed;
              },

              /**
               * Clears history for the given route and shows a spinner on route
               * change on item click.
               *
               * @param string route The section route name.
               */
              itemClick: function(route) {
                var url = routing.ngGenerateShort(route);
                history.clear(url);

                // Show spinner
                if (!this.changing[route] && !this.isActive(url)) {
                  this.changing[route] = true;
                }

                this.collapsed = this.pinned;

                // Collapse this for small screens
                if (this.forced) {
                  this.collapsed = true;
                }
              },

              /**
               * Updates the sidebar status on mouse enter event.
               */
              mouseEnter: function() {
                this.collapsed = false;
              },

              /**
               * Updates the sidebar status on mouse leave eventDAME.
               */
              mouseLeave: function() {
                this.collapsed = this.pinned;

                if (this.forced) {
                  this.collapsed = true;
                }
              },

              /**
               * Expands the sidebar.
               */
              open: function() {
                this.collapsed = false;
              },

              /**
               * Pins/unpins the sidebar.
               */
              pin: function() {
                this.pinned    = !this.pinned;
                this.collapsed = this.pinned;
              },

              /**
               * Returns the HTML for an item.
               *
               * @param Object item The item to render.
               *
               * @return string The HTML code for the given item.
               */
              renderItem: function(item) {
                var arrow     = '';
                var click     = '';
                var iconClass = '';
                var li        = itemTpl;
                var spinner   = '';
                var submenu   = '';
                var urls      = [];

                if (item.route) {
                  urls.push('ngModel.isActive(\''
                    + routing.ngGenerateShort(item.route) + '\')');
                }

                // Get children URLs
                if (item.items && item.items.length > 0) {
                  for (var i = 0; i < item.items.length; i++) {
                    if (item.items[i].route) {
                      urls.push('ngModel.isActive(\''
                        + routing.ngGenerateShort(item.items[i].route) + '\')');
                    }
                  }
                }

                urls = urls.join(' || ');

                if (item.route && item.click) {
                  click = ' ng-click="ngModel.itemClick(\'' + item.route + '\')"';
                }

                // Item with route
                if (item.route) {
                  li = li.replace('#', routing.ngGenerate(item.route));
                }

                // Custom icon class
                if (item.icon) {
                  iconClass = ' ' + item.icon;
                }

                // Spinner
                if (item.route) {
                  spinner = ' ng-class="{ \'fa-spin fa-circle-o-notch\': ngModel.isChanging(\''
                    + item.route + '\')}"';
                }

                // Arrow & sub-menu
                if (item.items && item.items.length > 0) {
                  arrow = '<span class="arrow" ng-class="{ \'open\':'
                    + urls + '}"></span>';

                  submenu += '<ul class="sub-menu">'

                  for (var i = 0; i < item.items.length; i++) {
                    submenu += this.renderItem(item.items[i]);
                  }

                  submenu += '</ul>';
                }

                li = li.replace('[urls]', urls);
                li = li.replace('[click]', click);
                li = li.replace('[icon-class]', iconClass);
                li = li.replace('[spinner]', spinner);
                li = li.replace('[name]', item.name);
                li = li.replace('[arrow]', arrow);
                li = li.replace('[submenu]', submenu);

                return li;
              },

              /**
               * Returns the HTML for a sidebar.
               *
               * @return string The HTML code for the given sidebar
               */
              renderSidebar: function() {
                var border    = '';
                var div       = sidebarTpl;
                var inverted  = '';
                var footer    = '';
                var items     = '';
                var position  = ''
                var swipeable = '';

                if (this.position != 'left') {
                  position = ' on-right'
                }

                if (this.inverted) {
                  inverted = ' inverted'
                }

                if (this.swipeable) {
                  if (this.position == 'left') {
                    swipeable = ' ng-swipe-left="ngModel.mouseLeave()" ng-swipe-right="ngModel.mouseEnter()"';
                  } else {
                    swipeable = ' ng-swipe-left="ngModel.mouseEnter()" ng-swipe-right="ngModel.mouseLeave()"';
                  }
                }

                if (this.footer) {
                  footer = footerTpl;
                }

                if (this.pinnable) {
                  border = '<div class="sidebar-border ng-cloak"></div>'
                }

                for (var i = 0; i < this.data.items.length; i++) {
                  items += this.renderItem(this.data.items[i]);
                };

                div = div.replace('[class]', this.class);
                div = div.replace('[id]', this.id);

                div = div.replace('[footer]', footer);
                div = div.replace('[border]', border);
                div = div.replace('[items]', items);
                div = div.replace('[inverted]', inverted);
                div = div.replace('[position]', position);
                div = div.replace('[swipeable]', swipeable);
                div = div.replace('[ngModel]', attrs['ngModel']);

                return div;
              },

              /**
               * Returns the HTML for the current model.
               *
               * @return string The HTML code for the current model.
               */
              render: function() {
                return this.renderSidebar(this.model);
              },

              /**
               * Toggles the sidebar.
               */
              toggle: function()
              {
                if (this.collapsed) {
                  this.open();
                } else {
                  this.close();
                }
              }
            };

            /**
             * Restart the loading status for sidebar and check the top margin.
             *
             * @param Object event The event object.
             * @param array  args  The list of arguments.
             */
            $rootScope.$on('$routeChangeSuccess', function (event, next, current) {
                $scope.ngModel.changed();
            });

            /**
             * Updates sidebar status when window width changes.
             *
             * @param integer nv New width value.
             * @param integer ov Old width value.
             */
            $scope.$watch(function() { return $window.innerWidth }, function(nv, ov) {
              $scope.ngModel.check();
            });

            var html = $scope.ngModel.render();
            var e    = $compile(html)($scope);

            e.find('.sidebar').bind('mouseenter', function() {
              $scope.ngModel.mouseEnter();
              $scope.$apply();
            });

            e.find('.sidebar').bind('mouseleave', function() {
              $scope.ngModel.mouseLeave();
              $scope.$apply();
            })

            e.find('li > a').on('click', function (e) {
              var item = $(this).parent();
              var visible = item.hasClass('open');
              var submenu = $(this).next();

              // Close all opened menus
              item.parent().find('li.open .arrow.open').removeClass('open');
              item.parent().find('li.open .sub-menu').slideUp(200, function() {
                item.parent().find('li.open').removeClass('open');
              });

              if ($(this).next().hasClass('sub-menu') === false) {
                  return;
              }

              if (!visible) {
                  item.find('.arrow').first().addClass('open');

                  // Open sub-menu
                  submenu.slideDown(200, function() {
                    item.addClass('open');
                  });
              }

              e.preventDefault();
            });

            dft.replaceWith(e);

            if ($scope.ngModel.pinnable) {
               e.find('.sidebar-border').on('click', function (e) {
                $scope.ngModel.pin();
                $scope.$apply();
              });
            }
          });
        }
      }
    }
  ]);
