(function () {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.sidebar
   *
   * @requires onm.history
   * @requires onm.routing
   *
   * @description
   *   The `onm.sidebar` module provides a factory and a directive to create and
   *   manipulate page sidebars.
   */
  angular.module('onm.sidebar', ['onm.history', 'onm.routing'])
    /**
     * @ngdoc factory
     * @name  Sidebar
     *
     * @requires $http
     * @requires $location
     * @requires $window
     * @requires history
     * @requires routing
     *
     * @description
     *   Factory to create and configure new sidebar instances.
     */
    .factory(
      'Sidebar', ['$http', '$location', '$window', 'history', 'routing',
      function($http, $location, $window, history, routing) {
        /**
         * Default template for the sidebar.
         *
         * @type {String}
         */
        var defaultSidebarTpl = '<div class="sidebar"[ngAttrs]>' +
          '<div class="sidebar-wrapper">' +
            '<div class="spinner">' +
              '<i class="fa fa-circle-o-notch fa-3x fa-spin"></i>' +
            '</div>' +
          '</div>' +
        '</div>';

        /**
         * Template for the sidebar footer.
         *
         * @type {String}
         */
        var footerTpl = '<div class="sidebar-footer-widget">' +
          '<ul>' +
            '<li class="support"><li>' +
            '<li class="pin" ng-click="ngModel.pin()" tooltip="[% ngModel.data.translations[\'Show/hide sidebar\']%]" tooltip-placement="right">' +
              '<i class="fa fa-lg" ng-class="{ \'fa-angle-double-left\': ngModel.isPinned(), \'fa-angle-double-right\': !ngModel.isPinned()}"></i>' +
            '</li>' +
          '</ul>' +
        '</div>';

        /**
         * Template for a sidebar item.
         *
         * @type {String}
         */
        var itemTpl = '<li ng-class="{\'active open\': [urls]}"[click]>' +
          '<a ng-href="#">' +
            '<i class="fa[icon-class]"[spinner]></i>' +
            '<span class="title">[name]</span>' +
            '[arrow]' +
          '</a>' +
          '[submenu]' +
        '</li>';

        /**
         * Template for the sidebar.
         *
         * @type {String}
         */
        var sidebarTpl = '<div class="[class][position][inverted]"[id][ngAttrs][swipeable] ng-mouseleave="ngModel.mouseLeave()">' +
          '<div class="overlay" ng-click="ngModel.open()" ng-mouseenter="ngModel.mouseEnter()"></div>' +
          '<div class="sidebar-wrapper">' +
            '<scrollable>' +
              '<ul>' +
                '[items]' +
              '</ul>' +
            '</scrollable>' +
          '</div>' +
          '[footer]' +
        '</div>' +
        '[border]';

        /**
         * @memberOf Sidebar
         *
         * @description
         *   Default values for sidebar.
         *
         * @type {Object}
         */
        this.defaults = {
          changing:  false,
          class:     'sidebar',
          collapsed: false,
          data:      {},
          footer:    false,
          forced:    false,
          id:        null,
          inverted:  false,
          pinnable:  true,
          pinned:    true,
          position:  'left',
          swipeable: true,
          threshold: 992
        };

        /**
         * @function init
         * @memberOf Sidebar
         *
         * @description
         *   Creates and returns a new sidebar instance.
         *
         * @param Object options The sidebar options.
         *
         * @return Object A new sidebar instance.
         */
        this.init = function(options) {
          var sidebar = angular.extend({}, this.defaults, options);

          /**
           * Resets the changing status for the sidebar.
           */
          sidebar.changed = function() {
            this.changing = {};
          };

          /**
           * Checks the sidebar status basing on the current window width.
           */
          sidebar.check = function() {
            this.forced    = false;
            this.collapsed = !this.pinned;

            if ($window.innerWidth < this.threshold) {
              this.forced    = true;
              this.collapsed = true;
            }
          };

          /**
           * Collapses the sidebar.
           */
          sidebar.close = function() {
            this.collapsed = true;
          };

          /**
           * Returns the default HTML for sidebar.
           *
           * @param {Object} attrs Object with angular attributes.
           *
           * @return string The default HTML code for the sidebar.
           */
          sidebar.default = function(attrs) {
            var ngAttrs = '';

            for (var key in attrs) {
              var newKey = key.replace(/([A-Z]{1})/, '-$1'.toLowerCase());
              ngAttrs += ' ' + newKey + '="' + attrs[key] + '"';
            }

            defaultSidebarTpl = defaultSidebarTpl.replace('[ngAttrs]', ngAttrs);

            return defaultSidebarTpl;
          };

          /**
           * Updates the model for the current sidebar.
           *
           * @param object sidebar The sidebar values.
           */
          sidebar.init = function(route) {
            return $http.get(routing.generate(route)).then(function(response) {
              angular.extend(sidebar.model, response.data);
            });
          };

          /**
           * Checks if an URL is active.
           *
           * @param string url The URL to check.
           *
           * @return boolean True if the URL is active. Otherwise, return false.
           */
          sidebar.isActive = function(url) {
            return $location.path().indexOf(url.replace('#', '')) !== -1;
          };

          /**
           * Checks if an item is active (waiting for the response from server)
           * given its route.
           *
           * @param string route The route name.
           *
           * @return boolean True if the item active. Otherwise, returns false.
           */
          sidebar.isChanging = function(route) {
            return this.changing && this.changing[route];
          };

          /**
           * Returns the current sidebar status.
           *
           * @return boolean True if the sidebar is collapsed. Otherwise, returns
           *                 false
           */
          sidebar.isCollapsed = function() {
            return this.collapsed;
          };

          /**
           * Returns the current sidebar pinned  status.
           *
           * @return boolean True if the sidebar is pinned. Otherwise, returns
           *                 false
           */
          sidebar.isPinned = function() {
            return this.pinned;
          };

          /**
           * Clears history for the given route and shows a spinner on route
           * change on item click.
           *
           * @param string route The section route name.
           */
          sidebar.itemClick = function(route) {
            var url = routing.ngGenerateShort(route);
            history.clear(url);

            // Show spinner
            if (!this.changing[route] && !this.isActive(url)) {
              this.changing[route] = true;
            }

            this.collapsed = !this.pinned;

            // Collapse this for small screens
            if (this.forced) {
              this.collapsed = true;
            }
          };

          /**
           * Updates the sidebar status on mouse enter event.
           */
          sidebar.mouseEnter = function() {
            this.collapsed = false;
          };

          /**
           * Updates the sidebar status on mouse leave eventDAME.
           */
          sidebar.mouseLeave = function() {
            this.collapsed = !this.pinned;

            if (this.forced) {
              this.collapsed = true;
            }
          };

          /**
           * Expands the sidebar.
           */
          sidebar.open = function() {
            this.collapsed = false;
          };

          /**
           * Pins/unpins the sidebar.
           */
          sidebar.pin = function() {
            this.pinned    = !this.pinned;
            this.collapsed = !this.pinned;
          };

          /**
           * Returns the HTML for an item.
           *
           * @param Object item The item to render.
           *
           * @return string The HTML code for the given item.
           */
          sidebar.renderItem = function(item) {
            var arrow     = '';
            var click     = '';
            var iconClass = '';
            var li        = itemTpl;
            var spinner   = '';
            var submenu   = '';
            var urls      = [];

            if (item.route) {
              urls.push('ngModel.isActive(\'' +
                routing.ngGenerateShort(item.route) + '\')');
            }

            // Get children URLs
            if (item.items && item.items.length > 0) {
              for (var i = 0; i < item.items.length; i++) {
                if (item.items[i].route) {
                  urls.push('ngModel.isActive(\'' +
                    routing.ngGenerateShort(item.items[i].route) + '\')');
                }
              }
            }

            urls = urls.join(' || ');

            if (item.route && item.click) {
              click = ' ng-click="ngModel.itemClick(\'' + item.route + '\')"';
            }

            // Item with route
            if (item.route) {
              li = li.replace('#', routing.ngGenerate(item.route) + '/');
            }

            // Custom icon class
            if (item.icon) {
              iconClass = ' ' + item.icon;
            }

            // Spinner
            if (item.route) {
              spinner = ' ng-class="{ \'fa-spin fa-circle-o-notch\': ngModel.isChanging(\'' +
                item.route + '\'), \'' + item.icon + '\': !ngModel.isChanging(\'' +
                item.route + '\') }"';
            }

            // Arrow & sub-menu
            if (item.items && item.items.length > 0) {
              arrow = '<span class="arrow" ng-class="{ \'open\':' + urls +
                '}"></span>';

              submenu += '<ul class="sub-menu">';

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
          };

          /**
           * Returns the HTML for a sidebar.
           *
           * @param {Object} attrs Object with angular attributes.
           *
           * @return string The HTML code for the given sidebar
           */
          sidebar.renderSidebar = function(attrs) {
            var border    = '';
            var div       = sidebarTpl;
            var id        = '';
            var inverted  = '';
            var footer    = '';
            var items     = '';
            var position  = '';
            var swipeable = '';
            var ngAttrs = '';

            var ngAttrs = '';

            for (var key in attrs) {
              var newKey = key.replace(/([A-Z]{1})/, '-$1'.toLowerCase());
              ngAttrs += ' ' + newKey + '="' + attrs[key] + '"';
            }

            if (this.id) {
              id = ' id="' + this.id + '"';
            }

            if (this.position !== 'left') {
              position = ' on-right';
            }

            if (this.inverted) {
              inverted = ' inverted';
            }

            if (this.swipeable) {
              if (this.position === 'left') {
                swipeable = ' ng-swipe-left="ngModel.mouseLeave()" ng-swipe-right="ngModel.mouseEnter()"';
              } else {
                swipeable = ' ng-swipe-left="ngModel.mouseEnter()" ng-swipe-right="ngModel.mouseLeave()"';
              }
            }

            if (this.footer) {
              footer = footerTpl;
            }

            if (this.pinnable) {
              border = '<div class="sidebar-border ng-cloak"' + ngAttrs + ' ng-click="ngModel.pin()"></div>';
            }

            for (var i = 0; i < this.data.items.length; i++) {
              items += this.renderItem(this.data.items[i]);
            }

            div = div.replace('[class]', this.class);
            div = div.replace('[id]', id);
            div = div.replace('[footer]', footer);
            div = div.replace('[border]', border);
            div = div.replace('[items]', items);
            div = div.replace('[inverted]', inverted);
            div = div.replace('[ngAttrs]', ngAttrs);
            div = div.replace('[position]', position);
            div = div.replace('[swipeable]', swipeable);

            return div;
          };

          /**
           * Returns the HTML for the current model.
           *
           * @param {Object} attrs Object with angular attributes.
           *
           * @return string The HTML code for the current model.
           */
          sidebar.render = function(attrs) {
            return this.renderSidebar(attrs);
          };

          /**
           * Closes the sidebar on small devices with a swipe event.
           */
          sidebar.swipeClose = function() {
            if (!$('html').hasClass('touch')) {
              return false;
            }

            sidebar.close();
          };

          /**
           * Open the sidebar on small devices with a swipe event.
           */
          sidebar.swipeOpen = function() {
            if (!$('html').hasClass('touch')) {
              return false;
            }

            sidebar.open();
          };

          /**
           * Toggles the sidebar.
           */
          sidebar.toggle = function() {
            if (this.collapsed) {
              this.open();
            } else {
              this.close();
            }
          };

          return sidebar;
        };

        return this;
      }
    ])

    /**
     * @ngdoc directive
     * @name  sidebar
     *
     * @requires $compile
     * @requires $http
     * @requires $location
     * @requires $rootScope
     * @requires $window
     * @requires routing
     * @requires Sidebar
     *
     * @description
     *   Directive to create sidebars.
     *
     *  ###### Attributes:
     *  - **`footer`**: Whether to add the sidebar footer. (Optional)
     *  - **`inverted`**: Whether to invert the sidebar. (Optional)
     *  - **`ng-model`**: The sidebar object. (Required)
     *  - **`position`**: Whether to place the sidebar. (Optional)
     *  - **`pinnable`**: Whether the sidebar is pinnable. (Optional)
     *  - **`swipeable`**: Whether the sidebar is swipeable. (Optional)
     *  - **`src`**: The route name to request the sidebar model. (Required)
     *
     * @example
     * <!-- Create a sidebar at the left  -->
     * <sidebar class="sidebar" footer="true" ng-model="sidebar" position="left" src="manager_ws_sidebar_list" pinnable="true"></sidebar>
     */
    .directive('sidebar', ['$compile', '$filter', '$http', '$rootScope', '$window',
      'routing', 'Sidebar',
      function($compile, $filter, $http, $rootScope, $window, routing, Sidebar) {
        return {
          restrict: 'E',
          scope: {
            ngModel: '='
          },
          link: function($scope, elm, attrs) {
            if (!attrs.src) {
              return;
            }

            // Get angular attributes (ng-class, ng-show, ...)
            var angularAttrs = {};
            for (var key in attrs) {
              if (key !== 'ngModel' && /ng([A-Z][a-x]*)+/.test(key)) {
                angularAttrs[key] = attrs[key];
              }
            }

            $scope.ngModel = Sidebar.init({
              class:     attrs.class ? attrs.class : 'sidebar',
              footer:    attrs.footer === 'true' ? true : false,
              id:        attrs.id ? attrs.id : null,
              inverted:  attrs.inverted === 'true' ? true : false,
              pinnable:  attrs.pinnable === 'true' ? true : true,
              position:  attrs.position && attrs.position === 'right' ? attrs.position : 'left',
              swipeable: attrs.swipeable === 'true' ? attrs.swipeable : true
            });

            var dft = $compile($scope.ngModel.default(angularAttrs))($scope);
            elm.replaceWith(dft);

            var url = routing.generate(attrs.src);
            return $http.get(url).then(function(response) {
              $scope.ngModel.data = response.data;

              // Restart the loading status for sidebar and check the top margin
              $rootScope.$on('$routeChangeSuccess', function () {
                  $scope.ngModel.changed();
              });

              // Updates sidebar status when window width changes
              $scope.$watch(function() {
                return $window.innerWidth;
              }, function() {
                $scope.ngModel.check();
              });

              var html = $scope.ngModel.render(angularAttrs);
              var e    = $compile(html)($scope);

              e.find('.sidebar').bind('mouseenter', function() {
                $scope.ngModel.mouseEnter();
                $scope.$apply();
              });

              e.find('.sidebar').bind('mouseleave', function() {
                $scope.ngModel.mouseLeave();
                $scope.$apply();
              });

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
                e.find('.sidebar-border').on('click', function () {
                  $scope.ngModel.pin();
                  $scope.$apply();
                });
              }
            });
          }
        };
      }
    ]);
})();

