/**
* onm.sidebar Module
*
* Description
*/
angular.module('onm.sidebar', [])
  .service('sidebar', ['$http', '$location', '$rootScope', '$window', 'history', 'routing',
    function($http, $location, $rootScope, $window, history, routing) {
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
                <img class="gravatar" email="[% user.email %]" image="1" size="32" width=32 height=32 >\
              </div>\
              <div class="username">\
                [% user.name %]\
              </div>\
            </a>\
            <div class="logout" ng-click="logout();">\
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
      var sidebarTpl = '<div class="[class]" id="[id]"[swipeable]>\
        <div class="overlay"></div>\
        <scrollable>\
          <div class="page-sidebar-wrapper">\
            <ul>\
              [items]\
            </ul>\
          </div>\
        </scrollable>\
        [footer]\
      </div>';

      /**
       * Sidebar definition
       *
       * @type Object
       */
      var sidebar = {
        collapsed: false,
        footer:    false,
        forced:    false,
        model:     {
          class: 'page-sidebar'
        },
        pinnable:  true,
        pinned:    false,
        threshold: 992, // Minimum window width for a static sidebar

        /**
         * Resets the changing status for the sidebar.
         */
        changed: function() {
          sidebar.changing = {};
        },

        /**
         * Checks the sidebar status basing on the current window width.
         */
        check: function() {
          sidebar.forced    = false;
          sidebar.collapsed = sidebar.pinned;

          if ($window.innerWidth < sidebar.threshold) {
            sidebar.forced    = true;
            sidebar.collapsed = true;
          }
        },

        /**
         * Collapses the sidebar.
         */
        close: function() {
          sidebar.collapsed = true;
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
          return sidebar.changing && sidebar.changing[route];
        },

        /**
         * Returns the current sidebar status.
         *
         * @return boolean True if the sidebar is collapsed. Otherwise, returns
         *                 false
         */
        isCollapsed: function() {
          return sidebar.collapsed;
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

          console.log(sidebar.changing);

          // Show spinner
          if (!sidebar.changing[route] && !sidebar.isActive(url)) {
            sidebar.changing[route] = true;
            console.log(sidebar.changing);
          }

          sidebar.current = sidebar.pinned;

          // Collapse sidebar for small screens
          if (sidebar.forced) {
            sidebar.collapsed = true;
          }
        },

        /**
         * Updates the sidebar status on mouse enter event.
         */
        mouseEnter: function() {
          sidebar.collapsed = false;
        },

        /**
         * Updates the sidebar status on mouse leave eventDAME.
         */
        mouseLeave: function() {
          sidebar.collapsed = sidebar.pinned;

          if (sidebar.forced) {
            sidebar.collapsed = true;
          }
        },

        /**
         * Expands the sidebar.
         */
        open: function() {
          sidebar.collapsed = false;
        },

        /**
         * Pins/unpins the sidebar.
         */
        pin: function() {
          sidebar.pinned = !sidebar.pinned;
          sidebar.collapsed = sidebar.pinned;
        },

        renderBorder: function() {
            return '<div class="layout-collapse-border ng-cloak" ng-click="sidebar.pin()"></div>';
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
            urls.push('sidebar.isActive(\''
              + routing.ngGenerateShort(item.route) + '\')');
          }

          // Get children URLs
          if (item.items && item.items.length > 0) {
            for (var i = 0; i < item.items.length; i++) {
              if (item.items[i].route) {
                urls.push('sidebar.isActive(\''
                  + routing.ngGenerateShort(item.items[i].route) + '\')');
              }
            }
          }

          urls = urls.join(' || ');

          if (item.route && item.click) {
            click = ' ng-click="sidebar.itemClick(\'' + item.route + '\')"';
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
            spinner = ' ng-class="{ \'fa-spin fa-circle-o-notch\': sidebar.isChanging(\''
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
         * @param  Object sidebar The sidebar to render.
         *
         * @return string The HTML code for the given sidebar
         */
        renderSidebar: function(sidebar) {
          var div       = sidebarTpl;
          var footer    = '';
          var items     = '';
          var swipeable = '';

          if (this.swipeable) {
            swipeable = ' ng-swipe-right="sidebar.mouseEnter()" ng-swipe-left="sidebar.mouseLeave()"';
          }

          if (this.model.footer) {
            footer = footerTpl;
          }

          for (var i = 0; i < sidebar.items.length; i++) {
            items += this.renderItem(sidebar.items[i]);
          };

          div = div.replace('[class]', sidebar.class);
          div = div.replace('[id]', sidebar.id);
          div = div.replace('[swipeable]', swipeable);
          div = div.replace('[items]', items);
          div = div.replace('[footer]', footer);

          return div;
        },

        /**
         * Returns the HTML for the current model.
         *
         * @return string The HTML code for the current model.
         */
        render: function() {
          return this.renderSidebar(this.model);
        }
      };

      return sidebar;
    }
  ])

  .directive('sidebar', ['$compile', 'sidebar',
    function($compile, sidebar){
      return {
        restrict: 'E',
        link: function($scope, elm, attrs) {
          if (!attrs['src']) {
            return;
          }

          if (attrs['class']) {
            sidebar.model.class = attrs['class'];
          }

          if (attrs['footer']) {
            sidebar.model.footer = attrs['footer'];
          }

          if (attrs['id']) {
            sidebar.model.id = attrs['id'];
          }

          var html = sidebar.default();
          var dft = $compile(html)($scope);

          elm.replaceWith(dft);

          sidebar.init(attrs['src']).then(function(response) {
            console.log(sidebar);
            var html = sidebar.render();
            var e    = $compile(html)($scope);

            e.bind('mouseenter', function() {
              sidebar.mouseEnter();
              $scope.$apply();
            });

            e.bind('mouseleave', function() {
              sidebar.mouseLeave();
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

            // Add sidebar border to pin/unpin
            if (attrs['pinnable']) {
              sidebar.pinnable = attrs['pinnable'];

              var html = sidebar.renderBorder();
              var border = $compile(html)($scope);
              e.after(border);
            }
          });
        }
      }
    }
  ]);
