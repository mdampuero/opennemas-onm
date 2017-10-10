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
    .controller('CategoryCtrl', ['$controller', '$rootScope', '$scope', 'http', 'messenger', 'routing', '$window',
      function($controller, $rootScope, $scope, http, messenger, routing, $window) {
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
          internal_category: 1
        };

        $scope.subcategories = [];

        /**
         * @function init
         * @memberOf SettingsCtrl
         *
         * @description
         *   Initizalice the class for .
         */
        $scope.init = function(category, extraData, multilanguageEnable, languageData) {
          $scope.loading = true;
          $scope.inmenu = false;

          $scope.languageData = languageData;

          $scope.category            = ('id' in category) ? category : $scope.category;
          $scope.subcategories       = extraData.subcategories;
          $scope.categories          = extraData.categories;
          $scope.modules             = extraData.modules;
          $scope.configurations      = extraData.configurations;
          $scope.categoryUrl         = extraData.image_path + '/sections/' + $scope.category.logo_path;
          $scope.multilanguageEnable = multilanguageEnable;

          $scope.pre();
          $scope.loading = false;

          // TODO implement the ajax request for category info
        };

        /**
         * @function save
         * @memberOf SettingsCtrl
         *
         * @description
         *   Saves settings.
         */
        $scope.save = function() {
          $scope.preSave();
          var data = $scope.category;
          $scope.saving = true;

          http.put('backend_ws_category_save', data)
            .then(function(response) {
              $scope.saving      = false;

              if ($scope.category.internal_category === 0) {
                $scope.category.internal_category = -1;
              }

              var reload = response.status === 201 && (
                !$scope.category.id ||
                $scope.category.id === '' ||
                $scope.category.id === null
              );
              $scope.category.id = response.data.category;

              messenger.post(response.data.message);

              if (reload) {
                setTimeout(function(){
                  $window.location.href = '/' + routing.generate('admin_category_show', {id: $scope.category.id});
                }, 2000);
              }
            }, function(response) {
              $scope.saving = false;
              if($scope.category.internal_category === 0) {
                $scope.category.internal_category = -1;
              }
              messenger.post(response.data);
            });
        };

        /**
         * Precalculation of params needed
         */
        $scope.pre = function() {
          $scope.languageData = $scope.languageData || {available:['default'], locale:'default'};

          // Initialize all the languages
          Object.keys($scope.languageData.available).forEach(function (langAux) {
            if (!$scope.category.title[langAux]) {
              $scope.category.title[langAux] = '';
            }

            if (!$scope.category.name[langAux]) {
              $scope.category.name[langAux] = '';
            }
          });

          if ($scope.category.internal_category === 0) {
            $scope.category.internal_category = -1;
          }

          $scope.lang = $scope.languageData.locale || $scope.languageData['default'];

          if (!$scope.category.internal_category) {
            $scope.category.internal_category = $scope.allowedCategories[0].code;
          }
          $scope.category.internal_category = $scope.category.internal_category.toString();

          $scope.subsectionCategories = [];
          for(var key in $scope.categories) {
            if($scope.category.id !== $scope.categories[key].id && $scope.categories[key].internal_category === 1) {
              $scope.subsectionCategories.push({'code':$scope.categories[key].id, 'value':$scope.categories[key].title});
            }
          }
          $scope.multiLanguageFields = ['title', 'name']
        };

        /**
         * Precalculation needed before save.
         */
        $scope.preSave = function() {
          if ($scope.category.internal_category === -1) {
            $scope.category.internal_category = 0;
          }
        };

        /**
         * This method load the slug text when the title value changes
         */
        $scope.loadSlug = function() {
          $scope.getSlug($scope.category.title[$scope.lang], function(response) {
              $scope.category.name[$scope.lang] = response.data.slug;
            }
          );
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
