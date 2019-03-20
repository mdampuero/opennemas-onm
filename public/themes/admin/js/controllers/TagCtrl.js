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
      '$controller', '$scope', '$timeout', 'cleaner',
      function($controller, $scope, $timeout, cleaner) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        /**
         * @inheritdoc
         */
        $scope.item = {
          description: '',
        };

        /**
         * @inheritdoc
         */
        $scope.routes = {
          create:   'api_v1_backend_tag_create',
          redirect: 'backend_tag_show',
          save:     'api_v1_backend_tag_save',
          show:     'api_v1_backend_tag_show',
          update:   'api_v1_backend_tag_update'
        };

        /**
         * @inheritdoc
         */
        $scope.getData = function() {
          var data = angular.extend({}, $scope.item);

          return cleaner.clean(data);
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
