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
         *  The settings object.
         *
         * @type {Object}
         */
        $scope.settings = { fields: [] };

        /**
         * @function add
         * @memberOf SubscriberConfigCtrl
         *
         * @description
         *   Adds a new field to the field list.
         */
        $scope.addField = function() {
          if (!$scope.settings.fields) {
            $scope.settings.fields = [];
          }
          if ($scope.settings.fields[$scope.settings.fields.length - 1].name !== '' &&
          $scope.settings.fields[$scope.settings.fields.length - 1].title !== '') {
            $scope.settings.fields.push({ name: '', title: '', type: 'text', required: false });
          }
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
              $scope.backup = angular.copy($scope.settings.fields);
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
          if (index in $scope.backup) {
            $scope.backup.splice(index, 1);
          }
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

        $scope.$watch('settings.fields', function(nv, ov) {
          if (!nv || nv === ov) {
            return;
          }
          nv.forEach(function(element, index) {
            if (index in $scope.backup &&
              $scope.backup[index].name === element.name) {
              return;
            }
            $scope.tm = $timeout(function() {
              $scope.getSlug(element.title, function(response) {
                element.name = response.data.slug;
              });
            }, 250);
          });
        }, true);
        $scope.list();
      }
    ]);
})();
