/**
 * Controller to implement common actions.
 *
 * @param Object $filter          The filter service.
 * @param Object $http            The http service.
 * @param Object $location        The location service.
 * @param Object $modal           The modal service.
 * @param Object $rootScope       The rootScope object.
 * @param Object $scope           The current scope.
 * @param Object $translate       The translation service.
 * @param Object $timeout         The timeout service.
 * @param Object $window          The window object.
 * @param Object paginationConfig The pagination configuration object.
 * @param Object routing          The routing service.
 * @param Object Sidebar          The sidebar factory.
 */
angular.module('BackendApp.controllers').controller('MasterCtrl', [
  '$compile', '$filter', '$http', '$location', '$modal', '$rootScope', '$scope', '$translate', '$timeout', '$window', 'anTinycon', 'paginationConfig', 'messenger', 'routing', 'Sidebar',
  function ($compile, $filter, $http, $location, $modal, $rootScope, $scope, $translate, $timeout, $window, anTinycon, paginationConfig, messenger, routing, Sidebar) {
    'use strict';

    /**
     * Flag to enable/disable forced notifications.
     *
     * @type Boolean
     */
    $scope.forced = true;

    /**
     * The current language.
     *
     * @type String
     */
    $scope.lang = 'en';

    /**
     * The routing service.
     *
     * @type Object
     */
    $scope.routing = routing;

    /**
     * The current sidebar.
     *
     * @type Object
     */
    $scope.sidebar = Sidebar.init();

    /**
     * Disables forced notifications.
     */
    $scope.disableForced = function() {
      $scope.forced = false;
    };

    /**
     * Configures the language, translates the pagination texts and
     * initializes the sidebar basing on the server status.
     *
     * @param string language The current language.
     */
    $scope.init = function(language) {
      $scope.lang = language;
      $translate.use(language);

      paginationConfig.nextText     = $filter('translate')('Next');
      paginationConfig.previousText = $filter('translate')('Previous');

      if ($('body').hasClass('unpinned-on-server')) {
        $scope.sidebar.pinned    = false;
        $scope.sidebar.collapsed = true;
      }
    };

    /**
     * Updates the content margin-top basing on the filters-navbar height.
     */
    $scope.checkFiltersBar = function() {
      $timeout(function() {
        if ($('.view:not(.ng-leave-active) .filters-navbar').length !== 1) {
          return false;
        }

        var margin = 50 + $('.filters-navbar').height() - 15;

        $('.content').css('margin-top', margin + 'px');
      }, 1000);
    };

    /**
     * @function getLatest
     * @memberOf NotificationCtrl
     *
     * @description
     *   Gets a list of notifications to display in dropdown.
     */
    $scope.getLatest = function() {
      var url = routing.generate('backend_ws_notifications_latest');

      $http.get(url).success(function(response) {
        $scope.notifications = response.results;
        anTinycon.setBubble(response.total);

        $scope.bounce = true;

        if ($scope.forced) {
          var i = 0;
          while (i < response.results.length && !$scope.notification) {
            if (response.results[i].forced == 1) {
              $scope.notification = response.results[i];
            }

            i++;
          }

          if ($scope.notification) {
            var tpl = '<div class="notification-item" ng-class="{ \'notification-item-visible\': notification.visible, \'notification-item-with-icon\': notification.style.icon }" ng-style="{ \'background-color\': notification.style.background_color }">' +
              '<div class="clearfix notification-item-content">' +
              '<div class="notification-icon" ng-style="{ \'color\': notification.style.background_color }">' +
              '<i class="fa fa-[% notification.style.icon %]"></i>' +
              '</div>' +
              '<div class="notification-body" ng-bind-html="notification.title ? notification.title : notification.body" ng-style="{ \'color\': notification.style.font_color }"></div>' +
              '</div>'+
              '</div>';

            var e = $compile(tpl)($scope);
            $('.content').prepend(e);

            $timeout(function() {
              $scope.notification.visible = true;
            }, 1000);
          }

          $timeout(function() {
            $scope.bounce = false;
          }, 1000);
        }
      });
    };

    /**
     * @function markAsRead
     * @memberOf NotificationCtrl
     *
     * @description
     *   Marks a notification as read.
     *
     * @param {Integer} index The index of the notification to mark.
     */
    $scope.markAsRead = function(id) {
      var url = routing.generate('backend_ws_notification_patch', { id: id });

      $http.patch(url).success(function() {
        $scope.notification.visible = false;

        $timeout(function() {
          $scope.notification = null;
        }, 1000);

        var i = 0;
        while (i < $scope.notifications.length &&
            $scope.notifications[i].id !== id) {
          i++;
        }

        if (i < $scope.notifications.length) {
          $scope.notifications.splice(i, 1);
          $scope.pulse = true;
          $timeout(function() { $scope.pulse = false; }, 1000);
        }
      });
    };

    $scope.xsOnly = function(event, callback, args) {
      if ($scope.windowWidth < 992) {
        callback(args);
      }
    };

    /**
     * Opens a new modal window.
     *
     * @param {String} name The modal name.
     */
    $scope.open = function(name, selected) {
      $modal.open({
        templateUrl: name,
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              selected: selected
            };
          },
          success: function() {
            return null;
          }
        }
      });
    };

    /**
     * Updates selected items current status.
     *
     * @param  mixed messages List of messages provided by the server.
     */
    $scope.renderMessages = function(messages) {
      var errors = 0;

      for (var i = 0; i < messages.length; i++) {
        var params = {
          id: new Date().getTime() + '_' + messages[i].id,
          message: messages[i].message,
          type: messages[i].type
        };

        messenger.post(params);

        if (messages[i].type === 'error') {
          errors++;
        }
      }
    };

    /**
     * Sends a request to update the sidebar pinned status in server.
     *
     * @param integer nv The new value.
     * @param integer ov The old value.
     */
    $scope.$watch('sidebar.pinned', function(nv, ov) {
      if (nv === ov) {
        return;
      }

      $http.put(routing.generate('admin_menu_sidebar_set'), {pinned: nv});
    });

    // Updates the sidebar status basing on the current window width
    $scope.$watch('windowWidth', function(nv) {
      $timeout(function() {
        if (nv < 992) {
          $scope.sidebar.forced = true;
          $scope.sidebar.collapsed = true;
        } else {
          $scope.sidebar.forced = false;
          $scope.sidebar.collapsed = !$scope.sidebar.pinned;
        }

        $('body').removeClass('pinned-on-server');
        $('body').removeClass('unpinned-on-server');
        $('body').removeClass('server-sidebar');
      }, 100);
    });

    // Update style for actions-navbar on window scroll
    $(window).bind('scroll', function() {
      $('.actions-navbar').removeClass('scrolled');
      $('.content-sidebar').removeClass('scrolled');

      if ($(window).scrollTop() > 0) {
        $('.actions-navbar').addClass('scrolled');
      }

      if ($('.content-sidebar').length > 0) {
        var scroll = $(window).scrollTop();
        var offset = $('.content-sidebar').offset().top;

        if (offset - scroll < 85) {
          $('.content-sidebar').addClass('scrolled');
        }
      }
    });
  }
]);
