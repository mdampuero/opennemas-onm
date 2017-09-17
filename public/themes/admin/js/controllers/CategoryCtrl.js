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
         *  The settings object with default values.
         *
         * @type {Object}
         */
        $scope.category = {};

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
        $scope.init = function() {
          $scope.loading = true;
          $scope.inmenu = false;
          if(categoryData) {
            $scope.category = categoryData.category;
            $scope.subcategories = categoryData.subcategories;
            $scope.categories = categoryData.categories;
            $scope.configurations = categoryData.configurations;
            $scope.internalCategories = categoryData.internal_categories;
            $scope.categoryUrl = categoryData.imagePath + '/sections/';
            $scope.languageData = categoryData.language_data;
            $scope.loading = false;
            $scope.lang = categoryData.language_data.default;
            return;
          }
          $scope.loading = false;

          // TODO implement the ajax request for caregory info
        };

        $scope.getInternalCategories = function(internal) {
          var prueba = $scope.internalCategories.allowedCategories.map(function(categoryKey) {
            var value = (categoryKey == 0)?'internal':$scope.internalCategories.internalCategories[categoryKey].title
            return {'code':categoryKey, 'value':value};
          });
          return prueba;
        };

        $scope.test = function() {
          alert('$scope.category.inmenu');
        };

        /**
         * @function save
         * @memberOf SettingsCtrl
         *
         * @description
         *   Saves settings.
         */
        $scope.save = function() {
          alert($scope.category.inmenu);
          $scope.category.inmenu = ($scope.category.inmenu == 1)?0:1;
          /**
          var data = $scope.post();

          $scope.saving = true;

          http.put('api_v1_backend_settings_save', data)
            .then(function(response) {
              $scope.saving = false;
              messenger.post(response.data);
            }, function(response) {
              $scope.saving = false;
              messenger.post(response.data);
            });
          */
        };

        $scope.internalCategoriesImgs = {
          7:  'fa-stack-overflow',
          9:  'fa-film',
          11: 'fa-pie-chart',
          10: 'fa-star',
          14: 'fa-newspaper-o',
          15: 'fa-book',
        };
      }
    ]);
})();
