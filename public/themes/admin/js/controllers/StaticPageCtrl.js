angular.module('BackendApp.controllers')

  /**
   * @ngdoc controller
   * @name  StaticPageCtrl
   *
   * @description
   *   Handles actions for static page inner
   *
   * @requires $controller
   * @requires $rootScope
   * @requires $scope
   */
  .controller('StaticPageCtrl', [
    '$controller', '$rootScope', '$scope',
    function($controller, $rootScope, $scope) {
      'use strict';

      // Initialize the super class and extend it.
      $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

      /**
       * @function init
       * @memberOf StaticPageCtrl
       * Method to init the static page controller
       *
       * @param {object} staticPage Static page to edit
       * @param {String} locale     Locale for the static page
       * @param {Array}  tags       Array with all the tags needed for the static page
       */
      $scope.init = function(staticPage, locale, tags) {
        $scope.tag_ids = staticPage ? staticPage : [];
        $scope.locale  = locale;
        $scope.tags    = tags;
      };
    }
  ]);
