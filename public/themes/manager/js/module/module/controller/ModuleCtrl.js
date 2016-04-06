(function () {
  'use strict';

  angular.module('ManagerApp.controllers')
    /**
     * @ngdoc controller
     * @name  ModuleCtrl
     *
     * @requires $filter
     * @requires $location
     * @requires $uibModal
     * @requires $scope
     * @requires itemService
     * @requires routing
     * @requires messenger
     *
     * @description
     *   Handles actions for module edition form
     */
    .controller('ModuleCtrl', [
      '$filter', '$http', '$location', '$uibModal', '$routeParams', '$scope', '$timeout', 'Cleaner', 'itemService', 'routing', 'messenger',
      function ($filter, $http, $location, $uibModal, $routeParams, $scope, $timeout, Cleaner, itemService, routing, messenger) {
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
          images:      [],
          metas:       { category: 'module', price: [ { 'value': 0, 'type': 'monthly' } ] },
          name:        { en: '', es: '', gl: '' },
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
          if (!$scope.module.metas.price) {
            $scope.module.metas.price = [];
          }

          $scope.module.metas.price.push({ value: 0, type: 'monthly' });
        };

        /**
         * @function addPrice
         * @memberOf ModuleCtrl
         *
         * @description
         *   Adds a new price to the list.
         */
        $scope.autocomplete = function(query) {
          var tags = [];

          for (var i = 0; i < $scope.extra.uuids.length;  i++) {
            var uuid = $scope.extra.uuids[i].toLowerCase();
            if (uuid.indexOf(query.toLowerCase()) !== -1) {
              tags.push($scope.extra.uuids[i]);
            }
          }

          return tags;
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
          var url = routing.generate('manager_ws_module_check',
              { uuid: $scope.module.uuid });

          if ($scope.module.id) {
            url = routing.generate('manager_ws_module_check',
              { id: $scope.module.id, uuid: $scope.module.uuid });
          }

          if (tm) {
            $timeout.cancel(tm);
          }

          tm = $timeout(function() {
            $http.get(url).then(function() {
              $scope.uuidValid = true;
            }, function(response) {
              messenger.post(response.data);
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
          $scope.module.metas.price.splice(index, 1);
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

          if ($scope.module.metas.modules_in_conflict) {
            $scope.module.metas.modules_in_conflict = $scope.module.metas
              .modules_in_conflict.map(function(a) { return a.text; });
          }

          if ($scope.module.metas.modules_included) {
            $scope.module.metas.modules_included = $scope.module.metas.modules_included
              .map(function(e) { return e.text; });
          }

          Cleaner.clean($scope.module);

          for (var key in $scope.module) {
            if (key === 'name' || key === 'description' || key === 'about' || key === 'metas') {
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
          }).success(function (response, status, headers) {
            messenger.post({ message: response, type: 'success' });
            $scope.saving = 0;

            if (!$scope.module.id && status === 201) {
              // Get new module id
              var url = headers().location;
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
          $scope.module = null;
        });

        // Check UUID on change
        $scope.$watch('module.uuid', function(nv, ov) {
          if (!nv || nv === ov) {
            return;
          }

          $scope.check();
        }, true);

        if ($routeParams.id) {
          itemService.show('manager_ws_module_show', $routeParams.id).then(
            function(response) {
              $scope.extra  = response.data.extra;
              $scope.module = response.data.module;

              if ($scope.extra.uuids.indexOf($scope.module.uuid) === -1) {
                $scope.custom = true;
              }
            }
          );
        } else {
          itemService.new('manager_ws_module_new').then(
            function(response) {
              $scope.extra = response.data.extra;
            }
          );
        }
      }
    ]);
})();
