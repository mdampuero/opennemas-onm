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
      '$controller', '$location', '$scope', '$window', 'http', 'linker', 'localizer',
      function($controller, $location, $scope, $window, http, linker, localizer) {
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
          create:   'api_v1_backend_category_create',
          redirect: 'backend_category_show',
          save:     'api_v1_backend_category_save',
          show:     'api_v1_backend_category_show',
          update:   'api_v1_backend_category_update'
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
          http.get('api_v1_backend_categories_list').then(function(response) {
            data.extra.categories = response.data.items;
          });

          if (data.item && data.item.logo_path) {
            $scope.cover = data.item.logo_path.replace($window.instanceMedia, '');
          }

          $scope.configure(data.extra);
          $scope.localize();
        };

        /**
         * @function localize
         * @memberOf CategoryCtrl
         *
         * @description
         *   Configures multilanguage-related services basing on the scope.
         */
        $scope.localize = function() {
          var lz = localizer.get($scope.data.extra.locale);

          $scope.item = lz.localize($scope.data.item,
            $scope.data.extra.keys, $scope.config.locale.selected);

          if (!$scope.config.linker.item) {
            $scope.config.linkers.item = linker.get($scope.data.extra.keys,
              $scope.config.locale.default, $scope, true);
          }

          $scope.config.linkers.item.setKey($scope.config.locale.selected);
          $scope.config.linkers.item.link($scope.data.item, $scope.item);
        };

        // Updates the logo_path when an image is selected
        $scope.$watch('cover', function(nv) {
          if (nv && !angular.isObject(nv)) {
            return;
          }

          $scope.item.logo_path = nv ? nv.path_img : null;
        }, true);
      }
    ]);
})();
