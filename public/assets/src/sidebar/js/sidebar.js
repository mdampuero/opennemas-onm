(function() {
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
  angular.module('onm.sidebar', [ 'onm.routing', 'onm.security' ])

    /**
     * @ngdoc factory
     * @name  Sidebar
     *
     * @requires $http
     * @requires $location
     * @requires $window
     * @requires routing
     * @requires security
     *
     * @description
     *   Factory to create and configure new sidebar instances.
     */
    .factory('Sidebar', [
      '$http', '$location', '$window', 'routing', 'security',
      function($http, $location, $window, routing, security) {
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
         * Template for a sidebar item.
         *
         * @type {String}
         */
        var itemTpl = '<script type="text/ng-template" id="item">' +
          '<a ng-href="[% ngModel.getUrl(item) %]">' +
            '<i class="fa [% item.icon %]"></i>' +
            '<span class="title">[% item.name %]</span>' +
            '<span class="arrow" ng-class="{ \'open\': false }" ng-if="item.items"></span>' +
          '</a>' +
          '<ul class="sub-menu" ng-if="item.items">' +
            '<li ng-class="{ \'active\': ngModel.isActive(item) }" ng-click="ngModel.itemClick(item)" ng-if="ngModel.isEnabled(item.security)" ng-repeat="item in item.items" ng-include="\'item\'"></li>' +
          '</ul>' +
        '</script>';

        /**
         * Template for sidebar
         *
         * @type {String}
         */
        var sidebarTpl = '<div class="sidebar [% ngModel.class %]" ng-class="{ \'sidebar-right\': ngModel.position === \'right\', \'inverted\': ngModel.inverted }" ng-show="ngModel.security.user" [id][ngAttrs][swipeable] ng-mouseleave="ngModel.mouseLeave()">' +
          '<div class="overlay" ng-click="ngModel.open()" ng-mouseenter="ngModel.mouseEnter()"></div>' +
          '<div class="sidebar-wrapper">' +
            '<scrollable>' +
              '<ul>' +
                '<li ng-class="{ \'active open\': ngModel.isActive(item) }" ng-click="ngModel.itemClick(item)" ng-if="ngModel.isEnabled(item.security)" ng-repeat="item in ngModel.data.items" ng-include="\'item\'"></li>' +
              '</ul>' +
            '</scrollable>' +
          '</div>' +
          '<div class="sidebar-footer-widget" ng-if="ngModel.footer">' +
            '<ul>' +
              '<li class="support"><li>' +
              '<li class="pin" ng-click="ngModel.pin()" tooltip="[% ngModel.data.translations[\'Show/hide sidebar\']%]" tooltip-placement="right">' +
                '<i class="fa fa-lg" ng-class="{ \'fa-angle-double-left\': ngModel.isPinned(), \'fa-angle-double-right\': !ngModel.isPinned()}"></i>' +
              '</li>' +
            '</ul>' +
          '</div>' +
        '</div>' +
        '<div class="sidebar-border ng-cloak" ng-click="ngModel.pin()" ng-if="ngModel.security.user && ngModel.pinnable"></div>' +
        itemTpl;

        /**
         * @memberOf Sidebar
         *
         * @description
         *   Default values for sidebar.
         *
         * @type {Object}
         */
        this.defaults = {
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

          // Add security service to sidebar
          sidebar.security = security;

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
           * @return {String} The default HTML code for the sidebar.
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
           * @param {Object} sidebar The sidebar values.
           */
          sidebar.init = function(route) {
            return $http.get(routing.generate(route)).then(function(response) {
              angular.extend(sidebar.model, response.data);
            });
          };

          /**
           * Returns the URL for the item.
           *
           * @param {Object} item The sidebar item.
           *
           * @return {String} The URL for the item.
           */
          sidebar.getUrl = function(item) {
            if (item.route) {
              var url = routing.ngGenerate(item.route);

              // Fix default url (# => #/)
              if (url === '#') {
                return '#/';
              }

              return url;
            }

            return '#';
          };

          /**
           * Checks if an URL is active.
           *
           * @param {String} item The item to check.
           *
           * @return {Boolean} True if the URL is active. False otherwise.
           */
          sidebar.isActive = function(item) {
            if (!item && !item.route && !item.items) {
              return $location.path() === '/';
            }

            if (item.route) {
              var url = routing.ngGenerate(item.route).replace('#', '');

              if (url === '') {
                url = '/';
              }

              return $location.path().indexOf(url) === 0;
            }

            var active = false;

            if (item.items) {
              for (var i = 0; i < item.items.length; i++) {
                active  = active || sidebar.isActive(item.items[i]);
              }
            }

            return active;
          };

          /**
           * Returns the current sidebar status.
           *
           * @return {Boolean} True if the sidebar is collapsed. False
           *                   otherwise.
           */
          sidebar.isCollapsed = function() {
            return this.collapsed;
          };

          /**
           * Checks if an item is enabled.
           *
           * @return {Boolean} True if the item is enabled. False otherwise.
           */
          sidebar.isEnabled = function(constraints) {
            var enabled = true;

            if (!constraints) {
              return enabled;
            }

            if (constraints.permission) {
              for (var i = 0; i < constraints.permission.length; i++) {
                enabled = enabled ||
                  security.hasPermission(constraints.permission[i]);
              }
            }

            return enabled;
          };

          /**
           * Returns the current sidebar pinned  status.
           *
           * @return {Boolean} True if the sidebar is pinned. False otherwise.
           */
          sidebar.isPinned = function() {
            return this.pinned;
          };

          /**
           * Clears history for the given route on item click.
           *
           * @param {String} route The section route name.
           */
          sidebar.itemClick = function(item) {
            if (!item.route) {
              return;
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
           * Returns the HTML for the current model.
           *
           * @param {Object} attrs Object with angular attributes.
           *
           * @return string The HTML code for the current model.
           */
          sidebar.render = function(attrs) {
            var div       = sidebarTpl;
            var id        = '';
            var swipeable = '';
            var ngAttrs   = '';

            for (var key in attrs) {
              var newKey = key.replace(/([A-Z]{1})/, '-$1'.toLowerCase());

              ngAttrs += ' ' + newKey + '="' + attrs[key] + '"';
            }

            if (this.id) {
              id = ' id="' + this.id + '"';
            }

            if (this.swipeable) {
              if (this.position === 'left') {
                swipeable = ' ng-swipe-left="ngModel.mouseLeave()" ng-swipe-right="ngModel.mouseEnter()"';
              } else {
                swipeable = ' ng-swipe-left="ngModel.mouseEnter()" ng-swipe-right="ngModel.mouseLeave()"';
              }
            }

            div = div.replace('[id]', id);
            div = div.replace('[ngAttrs]', ngAttrs);
            div = div.replace('[swipeable]', swipeable);

            return div;
          };

          /**
           * Closes the sidebar on small devices with a swipe event.
           */
          sidebar.swipeClose = function() {
            if (!$('html').hasClass('touch')) {
              return;
            }

            sidebar.close();
          };

          /**
           * Open the sidebar on small devices with a swipe event.
           */
          sidebar.swipeOpen = function() {
            if (!$('html').hasClass('touch')) {
              return;
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
    .directive('sidebar', [
      '$compile', '$filter', '$http', '$rootScope', '$window', 'routing', 'security', 'Sidebar',
      function($compile, $filter, $http, $rootScope, $window, routing, security, Sidebar) {
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
              if (key !== 'ngModel' && key !== 'ng-show' &&
                  /ng([A-Z][a-x]*)+/.test(key)) {
                angularAttrs[key] = attrs[key];
              }
            }

            $scope.ngModel = Sidebar.init({
              class:     attrs.class ? attrs.class : 'sidebar',
              footer:    attrs.footer === 'true',
              id:        attrs.id ? attrs.id : null,
              inverted:  attrs.inverted === 'true',
              pinnable:  attrs.pinnable === 'true',
              position:  attrs.position && attrs.position === 'right' ? attrs.position : 'left',
              swipeable: attrs.swipeable === 'true' ? attrs.swipeable : true
            });

            var dft = $compile($scope.ngModel.default(angularAttrs))($scope);
            var url = routing.generate(attrs.src);

            elm.replaceWith(dft);

            $http.get(url).then(function(response) {
              $scope.ngModel.data = response.data;

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

              $('body').on('click', '.sidebar li > a', function(e) {
                var item    = $(this).parent();
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
            });
          }
        };
      }
    ]);
})();
