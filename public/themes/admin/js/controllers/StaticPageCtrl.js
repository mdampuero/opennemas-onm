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
    '$controller', '$scope', '$timeout',
    function($controller, $scope, $timeout) {
      'use strict';

      // Initialize the super class and extend it.
      $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

      /**
       * @memberOf StaticPageCtrl
       *
       * @description
       *  The list of routes for the controller.
       *
       * @type {Object}
       */
      $scope.routes = {
        create:   'api_v1_backend_static_page_create',
        redirect: 'backend_static_page_show',
        save:     'api_v1_backend_static_page_save',
        show:     'api_v1_backend_static_page_show',
        update:   'api_v1_backend_static_page_update'
      };

      /**
       * @memberOf StaticPageCtrl
       *
       * @description
       *  The static page object.
       *
       * @type {Object}
       */
      $scope.item = {
        body: '',
        content_type_name: 'static_page',
        fk_content_type: 5,
        content_status: 0,
        description: '',
        favorite: 0,
        frontpage: 0,
        created: new Date(),
        starttime: null,
        endtime: null,
        thumbnail: null,
        title: '',
        type: 0,
        with_comments: 0,
        categories: [],
        related_contents: [],
        tags: [],
      };

      /**
       * @memberOf StaticPageCtrl
       *
       * @description
       *  Whether to refresh the item after a successful update.
       *
       * @type {Boolean}
       */
      $scope.refreshOnUpdate = true;

      /**
       * @function getItemId
       * @memberOf EventCtrl
       *
       * @description
       *   Returns the item id.
       *
       * @return {Integer} The item id.
       */
      $scope.getItemId = function() {
        return $scope.item.pk_content;
      };

      // Update slug when title is updated
      $scope.$watch('item.title', function(nv, ov) {
        if (!nv) {
          return;
        }

        if (!$scope.item.slug || $scope.item.slug === '') {
          if ($scope.tm) {
            $timeout.cancel($scope.tm);
          }

          $scope.tm = $timeout(function() {
            $scope.getSlug(nv, function(response) {
              $scope.item.slug = response.data.slug;
            });
          }, 2500);
        }
      }, true);
    }
  ]);
