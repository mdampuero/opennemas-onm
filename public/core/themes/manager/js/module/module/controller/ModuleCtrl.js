(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  ModuleCtrl
     *
     * @requires $http
     * @requires $location
     * @requires $routeParams
     * @requires $scope
     * @requires $timeout
     * @requires cleaner
     * @requires http
     * @requires routing
     * @requires messenger
     *
     * @description
     *   Handles actions for module edition form
     */
    .controller('ModuleCtrl', [
      '$http', '$location', '$routeParams', '$scope', '$timeout', 'cleaner', 'http', 'routing', 'messenger',
      function ($http, $location, $routeParams, $scope, $timeout, cleaner, http, routing, messenger) {
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
          about:       { en: '', es: '', gl: '' },
          category:    'module',
          description: { en: '', es: '', gl: '' },
          enabled:     0,
          images:      [],
          price:       [ { 'value': 0, 'type': 'monthly' } ],
          name:        { en: '', es: '', gl: '' },
          notes:       { en: '', es: '', gl: '' },
          terms:       { en: '', es: '', gl: '' },
          type:        'module'
        };

        /**
         * @function addPrice
         * @memberOf ModuleCtrl
         *
         * @description
         *   Adds a new price to the list.
         */
        $scope.addPrice = function() {
          if (!$scope.module.price) {
            $scope.module.price = [];
          }

          $scope.module.price.push({ value: 0, type: 'monthly' });
        };

        /**
         * @function autocomplete
         * @memberOf ModuleCtrl
         *
         * @description
         *   Adds a new price to the list.
         */
        $scope.autocomplete = function(query) {
          var route = {
              name: 'manager_ws_module_autocomplete',
              params: { uuid: query }
          };

          return http.get(route).then(function(response) {
            return response.data.extensions;
          });
        };

        $scope.uuidValid = true;

        var tm;
        /**
         * @function check
         * @memberOf ModuleCtrl
         *
         * @description
         *   Checks if the given UUID is valid.
         */
        $scope.check = function() {
          $scope.checking = true;
          var route = {
            name: 'manager_ws_module_check',
            params: { uuid: $scope.module.uuid }
          };

          if ($scope.module.id) {
            route.params.id = $scope.module.id;
          }

          if (tm) {
            $timeout.cancel(tm);
          }

          tm = $timeout(function() {
            http.get(route).then(function() {
              $scope.checking  = false;
              $scope.uuidValid = true;
            }, function(response) {
              messenger.post(response.data);
              $scope.checking  = false;
              $scope.uuidValid = false;
            });
          }, 500);
        };

        /**
         * @function removePrice
         * @memberOf ModuleCtrl
         *
         * @description
         *   Remove a price from the list.
         *
         * @param {Integer} index The position of the price in the list.
         */
        $scope.removePrice = function(index) {
          $scope.module.price.splice(index, 1);
        };

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
         * @function countStringsLeft
         * @memberOf ModuleCtrl
         *
         * @description
         *   Counts the number of remaining strings for a language.
         *
         * @param {String} lang The language to check.
         *
         * @return {Integer} The number of remaining strings.
         */
        $scope.countStringsLeft = function(lang) {
          var left = 0;

          if (!$scope.module.name || !$scope.module.name[lang]) {
            left++;
          }

          if (!$scope.module.description || !$scope.module.description[lang]) {
            left++;
          }

          if (!$scope.module.about || !$scope.module.about[lang]) {
            left++;
          }

          return left;
        };

        /**
         * @function removeFile
         * @memberOf ModuleCtrl
         *
         * @description
         *   Removes the current image file.
         */
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
          var url  = routing.generate('manager_ws_module_save');

          if ($scope.module.id) {
            url  = routing.generate('manager_ws_module_update', { id: $scope.module.id });
            data.append('_method', 'PUT');
          }

          if ($scope.module.modules_in_conflict) {
            $scope.module.modules_in_conflict = $scope.module
              .modules_in_conflict.map(function(a) { return a.text; });
          }

          if ($scope.module.modules_included) {
            $scope.module.modules_included = $scope.module.modules_included
              .map(function(e) { return e.text; });
          }

          cleaner.clean($scope.module);

          for (var key in $scope.module) {
            if (key !== 'images') {
              data.append(key, JSON.stringify($scope.module[key]));
            } else if ($scope.module[key] instanceof Array) {
              for (var i = 0; i <  $scope.module[key].length; i++) {
                data.append(key + '[' + i + ']', $scope.module[key][i]);
              }
            } else {
              data.append(key, $scope.module[key]);
            }
          }

          $http.post(url, data, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
          }).then(function (response) {
            messenger.post(response.data);
            $scope.saving = 0;

            if (response.status === 201) {
              var url = response.headers().location.replace('/managerws', '');
              $location.path(url);
            }
          }, function(response) {
            messenger.post(response.data);
            $scope.saving = 0;
          });
        };

        /**
         * @function toggleOverlay
         * @memberOf ModuleCtrl
         *
         * @description
         *   Toggles the overlay.
         */
        $scope.toggleOverlay = function() {
          $scope.overlay = !$scope.overlay;
        };

        // To execute on destroy
        $scope.$on('$destroy', function() {
          $scope.extra  = null;
          $scope.module = null;
        });

        // Check UUID on change
        $scope.$watch('module.uuid', function(nv, ov) {
          if (!nv || nv === ov) {
            return;
          }

          $scope.check();
        }, true);

        var route = 'manager_ws_module_new';

        if ($routeParams.id) {
          route = {
            name:   'manager_ws_module_show',
            params: { id:  $routeParams.id }
          };
        }

        http.get(route).then(function(response) {
          $scope.extra  = response.data.extra;

          if (response.data.module) {
            $scope.module = angular.merge($scope.module, response.data.module);
          }
        });
      }
    ]);
})();
