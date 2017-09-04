(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  SettingsCtrl
     *
     * @requires $controller
     * @requires $rootScope
     * @requires $scope
     *
     * @description
     *   Handles actions for paywall settings configuration form.
     */
    .controller('CategoryCtrl', ['$controller', '$rootScope', '$scope', 'http', 'messenger',
      function($controller, $rootScope, $scope, http, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf SettingsCtrl
         *
         * @description
         *  The instance properties that can be updated from settings.
         *
         * @type {Object}
         */
        $scope.instance = { country: null };

        /**
         * @memberOf SettingsCtrl
         *
         * @description
         *  Object for overlay-related flags.
         *
         * @type {Object}
         */
        $scope.overlay = {};

        /**
         * @memberOf SettingsCtrl
         *
         * @description
         *  The settings object with default values.
         *
         * @type {Object}
         */
        $scope.category = {
          /*
          pk_content_category: integer
          title:               string
          name:                string
          inmenu:              boolean
          posmenu:             integer
          internal_category:   integer
          fk_content_category: integer
          params:              array
          logo_path:           string
          color:               string

          google_analytics: [
            { api_key: '', base_domain: '', custom_var: '' }
          ],
          locale: {
            backend:   'en',
            frontend:  [],
            time_zone: 'UTC'
          },
          rtb_files: []
          */
        };

        $scope.subcategories = [];

        /**
         * @function list
         * @memberOf SettingsCtrl
         *
         * @param {Map} category The string to search by.
         *
         * @description
         *   Lists all settings.
         */
        $scope.list = function(categoryData) {

                              'allcategorys'          => $categories,
                    'configurations'        => s::get('section_settings'),
                    'category'              => $category,
                    'subcategories'         => $subcategorys,
                    'internalCategories'    => \ContentManager::getContentTypes()

          if(category) {
            $scope.category = categoryData.category;
            $scope.subcategories = categoryData.subcategories;
            $scope.allcategorys = categoryData.allcategorys;
            $scope.configurations = categoryData.configurations;
            $scope.internalCategories = categoryData.internalCategories;
          }
          $scope.loading = true;

          http.get('api_v1_backend_category_show').then(function(response) {
            $scope.category = response.data.category;
            $scope.subcategories = response.data.subcategories;

            $scope.loading = false;
          }, function() {
            $scope.loading = false;
          });
        };





      }
    ]);
})();
