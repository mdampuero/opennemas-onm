(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SettingsCtrl
     *
     * @requires $controller
     * @requires $scope
     *
     * @description
     *   Handles actions for category edit form.
     */
    .controller('CategoryCtrl', [
      '$controller', '$location', '$scope', '$timeout', '$window', 'http', 'linker',
      function($controller, $location, $scope, $timeout, $window, http, linker) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        /**
         * @inheritdoc
         */
        $scope.item = {
          description: '',
          fk_content_category: null
        };

        /**
         * @inheritdoc
         */
        $scope.routes = {
          createItem: 'api_v1_backend_category_create_item',
          getItem:    'api_v1_backend_category_get_item',
          redirect:   'backend_category_show',
          saveItem:   'api_v1_backend_category_save_item',
          updateItem: 'api_v1_backend_category_update_item'
        };

        /**
         * @inheritdoc
         */
        $scope.getItemId = function() {
          return $scope.item.pk_content_category;
        };

        /**
         * @inheritdoc
         */
        $scope.itemHasId = function() {
          return $scope.item.pk_content_category &&
            $scope.item.pk_content_category !== null;
        };

        /**
         * @inheritdoc
         */
        $scope.parseItem = function(data) {
          if (data.item && data.item.logo_path) {
            $scope.cover = data.item.logo_path.replace($window.instanceMedia, '');
          }

          $scope.configure(data.extra);
          $scope.localize($scope.data.item, 'item', true);
        };

        // Updates the logo_path when an image is selected
        $scope.$watch('cover', function(nv, ov) {
          if (!ov && !nv || nv && !angular.isObject(nv)) {
            return;
          }

          $scope.item.logo_path = nv ? nv.path_img : null;
        }, true);

        // Generates slug when flag changes
        $scope.$watch('flags.generate.slug', function(nv) {
          if ($scope.item.name || !nv || !$scope.item.title) {
            $scope.flags.generate.slug = false;

            return;
          }

          if ($scope.tm) {
            $timeout.cancel($scope.tm);
          }

          $scope.tm = $timeout(function() {
            $scope.getSlug($scope.item.title, function(response) {
              $scope.item.name           = response.data.slug;
              $scope.flags.generate.slug = false;
              $scope.flags.block.slug    = true;

              $scope.form.name.$setDirty(true);
            });
          }, 250);
        }, true);
      }
    ]);
})();
