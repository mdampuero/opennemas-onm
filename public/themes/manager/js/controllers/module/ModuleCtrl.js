(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  ModuleCtrl
     *
     * @requires $filter
     * @requires $location
     * @requires $modal
     * @requires $scope
     * @requires itemService
     * @requires routing
     * @requires messenger
     * @requires data
     *
     * @description
     *   Handles actions for module edition form
     */
    .controller('ModuleCtrl', [
      '$filter', '$http', '$location', '$modal', '$scope', 'itemService', 'routing', 'messenger', 'data',
      function ($filter, $http, $location, $modal, $scope, itemService, routing, messenger, data) {
        /**
         * @memberOf ModuleCtrl
         *
         * @description
         *   The language to edit.
         *
         * @type {String}
         */
        $scope.language = 'en';
        /**
         * @memberOf ModuleCtrl
         *
         * @description
         *   The module object.
         *
         * @type {Object}
         */
        $scope.module = {
          description: {
            en: '',
            es: '',
            gl: '',
          },
          images: [],
          name: {
            en: '',
            es: '',
            gl: '',
          },
          short_description: {
            en: '',
            es: '',
            gl: '',
          },
          type: 'module'
        };

        $scope.languages = {
          'en': 'English',
          'es': 'Spanish',
          'gl': 'Galician',
        };

        /**
         * @memberOf ModuleCtrl
         *
         * @description
         *   The template parameters.
         *
         * @type {Object}
         */
        $scope.extra = data.extra;

        /**
         * @function changeLanguage
         * @memberOf ModuleCtrl
         *
         * @description
         *   Changes the current language.
         *
         * @param {String} lang The language value.
         */
        $scope.changeLanguage = function(lang) {
          $scope.language = lang;
        };

        $scope.removeFile = function() {
          $scope.module.images = [];
          $('#image').val('');
        };

         /**
         * @function update
         * @memberOf ModuleCtrl
         *
         * @description
         *   Updates an module.
         */
        $scope.save = function() {
          $scope.saving = 1;

          var data = new FormData();
          var url  = routing.generate('manager_ws_module_create');

          if ($scope.module.id) {
            url  = routing.generate('manager_ws_module_update', { id: $scope.module.id });
            data.append('_method', 'PUT');
          }

          for (var key in $scope.module) {
            if (key === 'name' || key === 'description' || key === 'short_description') {
              data.append(key, JSON.stringify($scope.module[key]));
            } else if ($scope.module[key] instanceof Array) {
              for (var i = 0; i <  $scope.module[key].length; i++) {
                data.append(key + '_' + i, $scope.module[key][i]);
              }
            } else {
              data.append(key, $scope.module[key]);
            }
          }

          $http.post(url, data, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
          }).success(function (response) {
            messenger.post({ message: response, type: 'success' });
            $scope.saving = 0;

            if ($scope.module.id && response.status === 201) {
              // Get new module id
              var url = response.headers()['location'];
              var id  = url.substr(url.lastIndexOf('/') + 1);

              url = routing.ngGenerateShort(
                  'manager_module_show', { id: id });
              $location.path(url);
            }
          }).error(function(response) {
            messenger.post({ message: response, type: 'error' });
            $scope.saving = 0;
          });
        };

        $scope.toggleOverlay = function() {
          $scope.overlay = !$scope.overlay;
        };

        $scope.$on('$destroy', function() {
          $scope.module = null;
        });

        if (data.module) {
          $scope.module = data.module;

          if (!$scope.module.images) {
            $scope.module.images = [];
          }
        }
      }
    ]);
})();
