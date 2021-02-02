(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  TagCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $timeout
     * @requires cleaner
     *
     * @description
     *   Handles actions for tag edit form.
     */
    .controller('TagCtrl', [
      '$controller', '$scope', '$timeout', 'cleaner', 'http',
      function($controller, $scope, $timeout, cleaner, http) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        /**
         * @inheritdoc
         */
        $scope.item = {
          description: '',
          locale: null
        };

        /**
         * @inheritdoc
         */
        $scope.routes = {
          createItem:   'api_v1_backend_tag_create_item',
          getItem:      'api_v1_backend_tag_get_item',
          list:         'backend_tags_list',
          redirect:     'backend_tag_show',
          saveItem:     'api_v1_backend_tag_save_item',
          updateItem:   'api_v1_backend_tag_update_item',
          validateItem: 'api_v1_backend_tag_validate_item'
        };

        /**
         * @function isValid
         * @memberOf TagCtrl
         *
         * @description
         *   Validates the tag.
         */
        $scope.isValid = function() {
          if ($scope.tm) {
            $timeout.cancel($scope.tm);
            $scope.disableFlags('http');
          }

          if (!$scope.item.name) {
            return;
          }

          $scope.flags.http.validating = true;

          var route = {
            name:   $scope.routes.validateItem,
            params: { name: $scope.item.name, locale: $scope.item.locale }
          };

          $scope.tm = $timeout(function() {
            http.get(route).then(function() {
              $scope.disableFlags('http');
              $scope.form.name.$setValidity('exists', true);
            }, function(response) {
              $scope.disableFlags('http');
              $scope.form.name.$setValidity('exists', false);

              $scope.error = '<ul><li>' + response.data.map(function(e) {
                return e.message;
              }).join('</li><li>') + '</li></ul>';
            });
          }, 500);
        };

        // Generates slug when flag changes
        $scope.$watch('flags.generate.slug', function(nv) {
          if ($scope.item.slug || !nv || !$scope.item.name) {
            $scope.flags.generate.slug = false;

            return;
          }

          if ($scope.tm) {
            $timeout.cancel($scope.tm);
          }

          $scope.tm = $timeout(function() {
            $scope.getSlug($scope.item.name, function(response) {
              $scope.item.slug           = response.data.slug;
              $scope.flags.generate.slug = false;
              $scope.flags.block.slug    = true;

              $scope.form.slug.$setDirty(true);
            });
          }, 250);
        }, true);
      }
    ]);
})();
