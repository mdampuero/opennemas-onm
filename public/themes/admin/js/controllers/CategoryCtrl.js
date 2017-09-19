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
            $scope.allowedCategories = $scope.internalCategories.allowedCategories.map(function(categoryKey) {
              var value = (categoryKey == 0)?'Internal':$scope.internalCategories.internalCategories[categoryKey].title
              return {'code':categoryKey, 'value':value};
            });



            $scope.categoryUrl = categoryData.imagePath + '/sections/';
            $scope.languageData = categoryData.language_data;
            $scope.loading = false;
            $scope.pre();
            $scope.getL10nSupport();
            return;
          }
          $scope.loading = false;

          // TODO implement the ajax request for caregory info
        };

        $scope.getL10nSupport = function(lang) {
          $scope.titleAux = $scope.category.title[$scope.lang];
          $scope.nameAux = $scope.category.name[$scope.lang];
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
        $scope.save = function()
        {
          var data = $scope.post();

          $scope.saving = true;

          http.put('backend_ws_category_save', data)
            .then(function(response) {
              $scope.saving = false;
              $scope.category.id = response.data.category.id;
              messenger.post(response.data.message);
            }, function(response) {
              $scope.saving = false;
              messenger.post(response.data);
            });
        };

        $scope.pre = function() {
          Object.keys(categoryData.language_data.all).forEach(function (langAux) {
            if(!$scope.category.title[langAux]) {
              $scope.category.title[langAux] = '';
            }
            if(!$scope.category.name[langAux]) {
              $scope.category.name[langAux] = '';
            }
          });

          $scope.lang = categoryData.language_data.locale || categoryData.language_data.default;
        }

        $scope.post = function() {
          Object.keys(data.all).forEach(function (lang) {
            if(trim($scope.category.title[lang]) === '') {
              $scope.category.title.delete(lang);
            }
            if(trim($scope.category.name[lang]) === '') {
              $scope.category.name.delete(lang);
            }
          });
        }

        $scope.getL10nFlags = function ()
        {

        }

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
