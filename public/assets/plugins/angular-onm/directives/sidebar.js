/**
* onm.sidebar Module
*
* Description
*/
angular.module('onm.sidebar', [])
  .service('sidebar', ['$http', '$location', '$rootScope', '$window', 'routing',
    function($http, $location, $rootScope, $window, routing) {
      /**
       * Sidebar definition
       *
       * @type Object
       */
      var sidebar = {
        collapsed: false,
        forced:    false,
        model:     {
          class: 'page-sidebar'
        },
        pinned:    false,
        threshold: 992, // Minimum window width for a static sidebar

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
          return '<div class="page-sidebar">\
              <div style="position: relative; height: 100%;">\
                <div style="position: absolute; top: 50%; left: 50%; display: block; width: 40px; height: 40px; margin-top: -20px; margin-left: -20px;">\
                  <i class="fa fa-circle-o-notch fa-3x fa-spin"></i>\
                </div>\
              </div>\
            </div>';
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
         * Returns the current sidebar status.
         *
         * @return boolean True if the sidebar is collapsed. Otherwise, returns
         *                 false
         */
        isCollapsed: function() {
          return sidebar.collapsed;
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

        /**
         * Returns the HTML for an item.
         *
         * @param Object item The item to render.
         *
         * @return string The HTML code for the given item.
         */
        renderItem: function(item) {
          var urls = [];

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

          var tpl = '<li ng-class="{ \'active open\':'
            + urls.join(' || ') + '}">';

          if (item.route) {
            tpl += '<a ng-href="' + routing.ngGenerate(item.route) + '">'
          } else {
            tpl += '<a ng-href="#">';
          }

          // Add item icon
          tpl += '<i class="fa '+ (item.icon ? item.icon : '') +'"></i>'
          tpl += '<span class="title">' + item.name + '</span>'

          if (item.items && item.items.length > 0) {
            tpl += '<span class="arrow" ng-class="{ \'open\':'
              + urls.join(' || ') + '}"></span>';
          };

          tpl += '</a>'

          // Add sub-items
          if (item.items && item.items.length > 0) {
            tpl += '<ul class="sub-menu">'

            for (var i = 0; i < item.items.length; i++) {
              tpl += this.renderItem(item.items[i]);
            }

            tpl += '</ul>'
          }

          // Close item tags
          tpl += '</li>';

          return tpl;
        },

        /**
         * Returns the HTML for a sidebar.
         *
         * @param  Object sidebar The sidebar to render.
         *
         * @return string The HTML code for the given sidebar
         */
        renderSidebar: function(sidebar) {
          var tpl = '<div class="' + sidebar.class + '" id="' + sidebar.id
            + ' ng-swipe-right="sidebar.mouseEnter()" ng-swipe-left="sidebar.mouseLeave()">\
            <div class="overlay"></div>\
            <scrollable>\
              <div class="page-sidebar-wrapper">\
                <ul>';

          for (var i = 0; i < sidebar.items.length; i++) {
            tpl += this.renderItem(sidebar.items[i]);
          };

          tpl += '</ul>\
              </div>\
            </scrollable>\
          </div>';

          return tpl;
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
          if (!attrs['ngSrc']) {
            return;
          }

          var html = sidebar.default();
          var dft = $compile(html)($scope);

          elm.replaceWith(dft);

          sidebar.init(attrs['ngSrc']).then(function(response) {
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
          });
        }
      }
    }
  ]);
