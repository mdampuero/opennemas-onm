(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  SubscriberConfigCtrl
     *
     * @requires $controller
     * @requires $uibModal
     * @requires $scope
     *
     * @description
     *   Handles actions for advertisement inner.
     */
    .controller('SubscriberConfigCtrl', [
      '$controller', '$scope', 'cleaner', 'http', 'messenger', '$timeout',
      function($controller, $scope, cleaner, http, messenger, $timeout) {
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf SubscriberConfigCtrl
         *
         * @description
         *  The default object.
         *
         * @type {Object}
         */
        $scope.default = {
          required: false,
          type: 'text'
        };

        /**
         * @memberOf SubscriberConfigCtrl
         *
         * @description
         *  The settings object.
         *
         * @type {Object}
         */
        $scope.settings = {
          fields: []
        };

        /**
         * @function add
         * @memberOf SubscriberConfigCtrl
         *
         * @description
         *   Adds a new field to the field list.
         */
        $scope.addField = function(field) {
          if (!field) {
            return;
          }

          if (!$scope.settings.fields) {
            $scope.settings.fields = [];
          }

          $timeout(function() {
            $scope.getSlug(field, function(response) {
              $scope.settings.fields.push(Object.assign({ name: response.data.slug, title: field }, $scope.default));
            });
          }, 0);
        };

        /**
         * @function list
         * @memberOf SubscriberConfigCtrl
         *
         * @description
         *   Gets the list of settings.
         */
        $scope.list = function() {
          $scope.loading = true;

          http.get('api_v1_backend_subscriber_get_config')
            .then(function(response) {
              $scope.loading = false;
              if (response.data.settings) {
                $scope.settings = response.data.settings;
              }
            }, function(response) {
              $scope.loading = false;

              messenger.post(response.data);
            });
        };

        /**
         * @function removeField
         * @memberOf SubscriberConfigCtrl
         *
         * @description
         *   Removes a field from the field list.
         *
         * @param {Integer} index The index of the field to remove.
         */
        $scope.removeField = function(index) {
          $scope.settings.fields.splice(index, 1);
        };

        /**
         * @function save
         * @memberOf SubscriberConfigCtrl
         *
         * @description
         *   Saves the list of settings.
         */
        $scope.save = function() {
          $scope.saving = true;

          var data = cleaner.clean($scope.settings);

          http.put('api_v1_backend_subscriber_save_config', data)
            .then(function(response) {
              $scope.saving = false;

              messenger.post(response.data);
            }, function(response) {
              $scope.saving = false;

              messenger.post(response.data);
            });
        };

        $scope.list();
      }
    ]);
})();
