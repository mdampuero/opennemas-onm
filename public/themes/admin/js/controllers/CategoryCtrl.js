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
      '$controller', '$scope', '$window', 'http',
      function($controller, $scope, $window, http) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        /**
         * @inheritdoc
         */
        $scope.item = {
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

          if (data.item) {
            $scope.item  = angular.extend($scope.item, data.item);
            $scope.cover = $scope.item.logo_path.replace($window.instanceMedia, '');
          }
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
