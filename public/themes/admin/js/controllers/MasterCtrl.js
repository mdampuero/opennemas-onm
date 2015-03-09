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
 * @param Object sidebar          The sidebar factory.
 */
angular.module('BackendApp.controllers').controller('MasterCtrl', [
  '$filter', '$http', '$location', '$modal', '$rootScope', '$scope', '$translate', '$timeout', '$window', 'paginationConfig', 'routing', 'sidebar',
  function ($filter, $http, $location, $modal, $rootScope, $scope, $translate, $timeout, $window, paginationConfig, routing, sidebar) {
    'use strict';

    /**
     * The current sidebar.
     *
     * @type Object
     */
    $scope.sidebar = sidebar.init();

    /**
     * Configures the language, translates the pagination texts and
     * initializes the sidebar basing on the server status.
     *
     * @param string language The current language.
     */
    $scope.init = function(language) {
      $translate.use(language);

      paginationConfig.nextText     = $filter('translate')('Next');
      paginationConfig.previousText = $filter('translate')('Previous');

      if ($('body').hasClass('unpinned-on-server')) {
        $scope.sidebar.pinned    = false;
        $scope.sidebar.collapsed = true;
      }
    };

    /**
     * Scrolls the page to top.
     */
    $scope.scrollTop = function() {
      $('body').animate({ scrollTop: 0 }, 250);
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
     * Opens a new modal window.
     *
     * @param {String} name The modal name.
     */
    $scope.open = function(name) {
      $modal.open({
        templateUrl: name,
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return null;
          },
          success: function() {
            return null;
          }
        }
      });
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

    /**
     * Updates the sidebar status basing on the current window width.
     *
     * @param integer nv The new values.
     */
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
  }
]);
