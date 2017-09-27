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
     *   Handles actions for category edit form.
     */
    .controller('CategoryCtrl', ['$controller', '$rootScope', '$scope', 'http', 'messenger', 'routing',
      function($controller, $rootScope, $scope, http, messenger, routing) {
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
        $scope.category = {
          title:{},
          name:{},
        };

        $scope.subcategories = [];

        /**
         * @function init
         * @memberOf SettingsCtrl
         *
         * @description
         *   Initizalice the class for .
         */
        $scope.init = function() {
          $scope.loading = true;
          $scope.inmenu = false;
          if(categoryData) {
            $scope.category = categoryData.category || $scope.category;
            $scope.subcategories = categoryData.subcategories;
            $scope.categories = categoryData.categories;
            $scope.configurations = categoryData.configurations;
            $scope.internalCategories = categoryData.internal_categories;
            $scope.languageData = categoryData.language_data || $scope.languageData;
            $scope.categoryUrl = categoryData.image_path + '/sections/' + $scope.category.logo_path;

            $scope.pre();
            $scope.loading = false;
            return;
          }
          $scope.loading = false;

          // TODO implement the ajax request for caregory info
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
          var data = $scope.category;
          $scope.saving = true;

          http.put('backend_ws_category_save', data)
            .then(function(response) {
              $scope.saving = false;
              $scope.category.id = response.data.category;
              messenger.post(response.data.message);
            }, function(response) {
              $scope.saving = false;
              messenger.post(response.data);
            });
        };

        $scope.pre = function() {
          var languageData = $scope.languageData || {all:['default'], locale:'default'};
          Object.keys(languageData.all).forEach(function (langAux) {
            if(!$scope.category.title[langAux]) {
              $scope.category.title[langAux] = '';
            }
            if(!$scope.category.name[langAux]) {
              $scope.category.name[langAux] = '';
            }
          });

          $scope.lang = languageData['locale'] || languageData['default'];

          $scope.allowedCategories = $scope.internalCategories.allowedCategories.map(function(categoryKey) {
            var value = (categoryKey == 0)?'Internal':$scope.internalCategories.internalCategories[categoryKey].title
            return {'code':categoryKey, 'value':value};
          });
          if(!$scope.category.internal_category) {
            $scope.category.internal_category = $scope.allowedCategories[0].code;
          }

          $scope.subsectionCategories = [];
          for(var key in $scope.categories) {
            if($scope.category.id != $scope.categories[key].id) {
              $scope.subsectionCategories.push({'code':$scope.categories[key].id, 'value':$scope.categories[key].title});
            }
          }
        }

        $scope.createShowCategoryUrl = function(categoryId) {
          return routing.generate('admin_category_show', {id: categoryId});
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
