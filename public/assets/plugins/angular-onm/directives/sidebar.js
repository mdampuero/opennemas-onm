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
           * The default template for the sidebar border.
           *
           * @type string
           */
          var borderTpl = '<div class="layout-collapse-border ng-cloak[position]"></div>';

          /**
           * Default template for the sidebar.
           *
           * @type string
           */
          var defaultSidebarTpl = '<div class="page-sidebar">\
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
          var footerTpl = '<div class="footer-widget">\
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
          var sidebarTpl = '<div ng-class="{ \'collapsed\': [ngModel].isCollapsed() }">\
            <div class="[class][position]" id="[id]"[swipeable]>\
              <div class="overlay"></div>\
              <scrollable>\
                <div class="page-sidebar-wrapper">\
                  <ul>\
                    [items]\
                  </ul>\
                </div>\
              </scrollable>\
              [footer]\
            </div>\
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
              footer:    attrs['footer'] ? attrs['footer'] : false,
              forced:    false,
              id:        attrs['id'] ? attrs['id'] : null,
              class:     attrs['class'] ? attrs['class'] : 'page-sidebar',
              pinnable:  attrs['pinnable'] ? attrs['pinnable'] : true,
              pinned:    false,
              position:  attrs['position'] ? attrs['position'] : 'left',
              swipeable: attrs['swipeable'] ? attrs['swipeable'] : true,
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
               * Returns the HTML for the sidebar border.
               *
               * @return string The HTML code for the sidebar border.
               */
              renderBorder: function() {
                var border   = borderTpl;
                var position = '';


                if (this.position != 'left') {
                  position = ' border-on-right'
                }

                border = border.replace('[ngModel]', attrs['ngModel']);
                border = border.replace('[position]', position);

                return border;
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
                var div       = sidebarTpl;
                var footer    = '';
                var items     = '';
                var position  = '';
                var swipeable = '';

                if (this.position != 'left') {
                  position = ' page-sidebar-on-right'
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

                for (var i = 0; i < this.data.items.length; i++) {
                  items += this.renderItem(this.data.items[i]);
                };

                div = div.replace('[class]', this.class);
                div = div.replace('[id]', this.id);
                console.log(this.swipeable);
                console.log(swipeable);

                div = div.replace('[position]', position);
                div = div.replace('[swipeable]', swipeable);
                div = div.replace('[items]', items);
                div = div.replace('[footer]', footer);
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

            var html = $scope.ngModel.render();
            var e    = $compile(html)($scope);

            e.bind('mouseenter', function() {
              $scope.ngModel.mouseEnter();
              $scope.$apply();
            });

            e.bind('mouseleave', function() {
              $scope.ngModel.mouseLeave();
              $scope.$apply();
            })

            e.find('li > a').on('click', function (e) {
              var item = $(this).parent();
              var visible = item.hasClass('open');
              var submenu = $(this).next();

              // Close all opened menus
              item.parent().find('li.open .arrow.open').removeClass('open');
              item.parent().find('li.open .sub-menu').slideUp(200);
              item.parent().find('li.open .sub-menu').removeClass('open');
              item.parent().find('li.open').removeClass('open');

              if ($(this).next().hasClass('sub-menu') === false) {
                  return;
              }

              if (!visible) {
                  // Open sub-menu
                  item.addClass('open');
                  item.find('.arrow').addClass('open');
                  item.find('.arrow').addClass('active');
                  submenu.slideDown(200);
              }

              e.preventDefault();
            });

            dft.replaceWith(e);

            if ($scope.ngModel.pinnable) {
              var html = $scope.ngModel.renderBorder();
              var border = $compile(html)($scope);

              border.bind('click', function (e) {
                $scope.ngModel.pin();
                $scope.$apply();
              });
              e.after(border);
            }
          });
        }
      }
    }
  ]);
