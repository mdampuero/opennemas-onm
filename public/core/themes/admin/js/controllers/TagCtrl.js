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
     *
     * @description
     *   Handles actions for tag edit form.
     */
    .controller('TagCtrl', [
      '$controller', '$scope', '$timeout', 'http', 'messenger',
      function($controller, $scope, $timeout, http, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf TagCtrl
         *
         * @description
         *  An object to save the messages associated with the specific fields.
         *
         * @type {Object}
         */
        $scope.messages = {};

        /**
         * @inheritdoc
         */
        $scope.item = {
          description: '',
          locale: null,
          id: null
        };

        /**
         * @inheritdoc
         */
        $scope.incomplete = true;

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
            params: { name: $scope.item.name, id: $scope.item.id, locale: $scope.item.locale }
          };

          $scope.tm = $timeout(function() {
            http.get(route).then(function() {
              $scope.messages.name = null;
              $scope.disableFlags('http');
              $scope.form.name.$setValidity('exists', true);
            }, function(response) {
              messenger.post(response.data);
              // Save the error in a variable
              $scope.messages.name = response.data.shift().message;
              $scope.disableFlags('http');
              $scope.form.name.$setValidity('exists', false);
            });
          }, 500);
        };

        // Generates slug when flag changes
        $scope.$watch('flags.generate.slug', function(nv) {
          if ($scope.item.slug || !nv || !$scope.item.name || $scope.messages.name) {
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
