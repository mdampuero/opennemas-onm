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
      '$filter', '$location', '$modal', '$scope', 'itemService', 'routing', 'messenger', 'data',
      function ($filter, $location, $modal, $scope, itemService, routing, messenger, data) {
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

        /**
         * @function save
         * @memberOf ModuleCtrl
         *
         * @description
         *   Creates a new module.
         */
        $scope.save = function() {
          if ($scope.moduleForm.$invalid) {
            $scope.formValidated = 1;

            messenger.post({
              message: $filter('translate')('FormErrors'),
              type:    'error'
            });

            return false;
          }

          $scope.saving = 1;

          itemService.save('manager_ws_module_create', $scope.module)
            .then(function (response) {
              messenger.post({ message: response.data, type: 'success' });

              if (response.status === 201) {
                // Get new module id
                var url = response.headers()['location'];
                var id  = url.substr(url.lastIndexOf('/') + 1);

                url = routing.ngGenerateShort(
                  'manager_module_show', { id: id });
                $location.path(url);
              }

              $scope.saving = 0;
            }, function(response) {
              $scope.saving = 0;
              messenger.post({ message: response, type: 'error' });
            });
        };

         /**
         * @function update
         * @memberOf ModuleCtrl
         *
         * @description
         *   Updates an module.
         */
        $scope.update = function() {
          if ($scope.moduleForm.$invalid) {
            $scope.formValidated = 1;

            messenger.post({
              message: $filter('translate')('FormErrors'),
              type:    'error'
            });

            return false;
          }

          $scope.saving = 1;

          itemService.update('manager_ws_module_update', $scope.module.id,
            $scope.module).success(function (response) {
              messenger.post({ message: response, type: 'success' });
              $scope.saving = 0;
            }).error(function(response) {
              messenger.post({ message: response, type: 'error' });
              $scope.saving = 0;
            });
        };

        $scope.$on('$destroy', function() {
          $scope.module = null;
        });


        if (data.module) {
          $scope.module = data.module;
        }
      }
    ]);
})();
