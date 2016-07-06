/**
 * Controller to implement common actions.
 *
 * @param Object $filter          The filter service.
 * @param Object $http            The http service.
 * @param Object $location        The location service.
 * @param Object $uibModal           The modal service.
 * @param Object $rootScope       The rootScope object.
 * @param Object $scope           The current scope.
 * @param Object $translate       The translation service.
 * @param Object $timeout         The timeout service.
 * @param Object $window          The window object.
 * @param Object routing          The routing service.
 * @param Object Sidebar          The sidebar factory.
 */
angular.module('BackendApp.controllers').controller('MasterCtrl', [
  '$compile', '$filter', '$http', '$location', '$uibModal', '$rootScope', '$scope', '$translate', '$timeout', '$window', 'anTinycon', 'messenger', 'routing', 'Sidebar', 'webStorage',
  function ($compile, $filter, $http, $location, $uibModal, $rootScope, $scope, $translate, $timeout, $window, anTinycon, messenger, routing, Sidebar, webStorage) {
    'use strict';

    /**
     * Flag to enable/disable forced notifications.
     *
     * @type Boolean
     */
    $scope.force = true;

    /**
     * Array of forced notifications
     *
     * @type Array
     */
    $scope.forced = [];

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
     * @function disableForced
     * @memberOf MasterCtrl
     *
     * @description
     *   Disables forced notifications.
     */
    $scope.disableForced = function() {
      $scope.force = false;
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

      $http.get(url).then(function(response) {
        $scope.notifications = response.data.results.filter(function (a) {
          return !a.forced || parseInt(a.forced) !== 1;
        });

        anTinycon.setBubble($scope.notifications.length);

        $scope.bounce = true;

        if ($scope.force) {
          $scope.forced = response.data.results.filter(function (a) {
            if (parseInt(a.forced) !== 1) {
              return false;
            }

            var expire = webStorage.get('notification-' + a.id);

            if (!expire) {
              return true;
            }

            var now = moment();
            expire = moment(expire);

            return now.unix() > expire.unix();
          });

          if ($scope.forced.length > 0) {
            var tpl = '<ul class="notification-list notification-list-auto">' +
              '<li class="notification-list-item" id="notification-[% notification.id %]" ng-class="{ \'notification-list-item-with-icon\': notification.style.icon }" ng-repeat="notification in forced" ng-style="{ \'background-color\': notification.style.background_color,  \'border-color\': notification.style.background_color }">' +
                '<span class="notification-list-item-close pull-right pointer" ng-click="markForcedAsRead($index)" ng-if="notification.fixed != 1">' +
                  '<i class="fa fa-times" style="color: [% notification.style.font_color %] !important;"></i>' +
                '</span>' +
                '<a ng-href="[% routing.ngGenerateShort(\'backend_notifications_list\') %]">' +
                  '<div class="notification-icon" ng-if="notification.style.icon" ng-style="{ \'background-color\': notification.style.font_color, \'color\': notification.style.background_color }">' +
                    '<i class="fa fa-[% notification.style.icon %]"></i>' +
                  '</div>' +
                  '<div class="notification-body" ng-bind-html="notification.title ? notification.title : notification.body" ng-style="{ \'color\': notification.style.font_color }"></div>' +
                  '</div>' +
                '</a>' +
              '</li>' +
            '</ul>';

            var e = $compile(tpl)($scope);
            $('.content').first().prepend(e);
          }

          $timeout(function() {
            $scope.bounce = false;
            $scope.markAllForcedAsView();
          }, 1000);
        }
      });
    };

    /**
     * @function markAllAsView
     * @memberOf NotificationCtrl
     *
     * @description
     *   Marks a notification as view.
     *
     * @param {Integer} index The index of the notification to mark.
     */
    $scope.markAllAsView = function() {
      if (!$scope.notifications || $scope.notifications.length === 0) {
        return;
      }

      var url  = routing.generate('backend_ws_notifications_patch');
      var date = new Date();
      var ids  = $scope.notifications.map(function(e) { return e.id; });
      var data = {
        ids:       ids,
        view_date: $window.moment(date).format('YYYY-MM-DD HH:mm:ss')
      };

      $http.patch(url, data);
    };

    /**
     * @function markAllForcedAsView
     * @memberOf NotificationCtrl
     *
     * @description
     *   Marks a notification as view.
     *
     * @param {Integer} index The index of the notification to mark.
     */
    $scope.markAllForcedAsView = function() {
      if (!$scope.forced || $scope.forced.length === 0) {
        return;
      }

      var url  = routing.generate('backend_ws_notifications_patch');
      var date = new Date();
      var ids  = $scope.forced.map(function(e) { return e.id; });
      var data = {
        ids:       ids,
        view_date: $window.moment(date).format('YYYY-MM-DD HH:mm:ss')
      };

      $http.patch(url, data);
    };

    /**
     * @function markAsClicked
     * @memberOf MasterCtrl
     *
     * @description
     *   Marks a notification as clicked.
     *
     * @param {Integer} index The index of the notification to mark.
     */
    $scope.markAsClicked = function(id) {
      var url  = routing.generate('backend_ws_notification_patch', { id: id });
      var date = new Date();
      var data = {
        click_date: $window.moment(date).format('YYYY-MM-DD HH:mm:ss'),
        read_date: $window.moment(date).format('YYYY-MM-DD HH:mm:ss')
      };

      // Find notification to remove from list
      var notifications = $scope.notifications;
      var notification  = notifications.filter(function(e) {
        return parseInt(e.id) === parseInt(id);
      });

      if (notification.length === 0) {
        notifications = $scope.forced;
        notification  = notifications.filter(function(e) {
          return parseInt(e.id) === parseInt(id);
        });
      }

      if (notification.length > 0) {
        notification = notification[0];
      }

      $http.patch(url, data).then(function () {
        if (notification) {
          var index = notifications.indexOf(notification);
          notifications.splice(index, 1);
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
    $scope.markAsRead = function(index) {
      var notification = $scope.notifications[index];
      var date = new Date();

      var url = routing.generate('backend_ws_notification_patch',
          { id: notification.id });

      var data = { read_date: $window.moment(date).format('YYYY-MM-DD HH:mm:ss') };

      $http.patch(url, data).then(function() {
        $scope.notifications.splice(index, 1);
        $scope.pulse = true;
        $timeout(function() { $scope.pulse = false; }, 1000);
      });
    };

    /**
     * @function markForcedAsRead
     * @memberOf NotificationCtrl
     *
     * @description
     *   Marks a forced notification as read.
     *
     * @param {Integer} index The index of the notification to mark.
     */
    $scope.markForcedAsRead = function (index) {
      var notification = $scope.forced[index];
      var id           = 'notification-' + notification.id;
      var date         = new Date();

      date.setDate(date.getDate() + 1);
      date = $window.moment(date).format('YYYY-MM-DD HH:mm:ss');
      webStorage.local.set(id, date);

      $scope.pulse = true;
      $scope.forced.splice(index, 1);

      $timeout(function() {
        $scope.pulse = false;
      }, 250);
    };

    /**
     * @function xsOnly
     * @memberOf MasterCtrl
     *
     * @description
     *   Executes an action only for small devices.
     *
     * @param {Object}   event    The event object.
     * @param {Function} callback The action to execute.
     * @param {Object}   args     The action arguments.
     *
     * @return {type} description
     */
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
      $uibModal.open({
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

    // Prevent empty links to change angular route
    $('body').on('click', 'a', function (e) {
      if ($(this).attr('href') === '#') {
        e.preventDefault();
      }
    });

    // Mark notifications as clicked when clicking in notification-action
    $('body, .notification-list').on('click', '.notification-list-item a a', function (e) {
      e.stopPropagation();

      var target   = e.target.closest('li');
      var siblings = $(target.closest('ul')).find('li');

      for (var i = 0; i < siblings.length; i++) {
        if (siblings[i] === target) {
          var id = $(siblings[i]).attr('id').replace('notification-', '');
          $scope.markAsClicked(id);
        }
      }
    });
  }
]);
