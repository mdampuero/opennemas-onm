(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  PollConfigCtrl
     *
     * @requires $controller
     * @requires $location
     * @requires $scope
     * @requires $timeout
     * @requires http
     * @requires messenger
     * @requires linker
     * @requires localizer
     * @requires oqlEncoder
     *
     * @description
     *   Provides actions to list articles.
     */
    .controller('PollConfigCtrl', [
      '$controller', '$scope', 'cleaner', 'http', 'messenger',
      function($controller, $scope, cleaner, http, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf UserSettingsCtrl
         *
         * @description
         *  The extraFields object.
         *
         * @type {Object}
         */
        $scope.extraFields = {};
        $scope.saving = false;

        $scope.init = function(extraFields) {
          if (extraFields !== null) {
            $scope.extraFields = extraFields;
          }
        };

        /**
         * @function saveConf
         * @memberOf PollConfigCtrl
         *
         * @description
         *   Saves the configuration.
         */
        $scope.saveConf = function($event) {
          $event.preventDefault();

          var data = { extraFields: JSON.stringify(cleaner.clean($scope.extraFields)) };

          $scope.saving = false;
          http.put('api_v1_backend_extra_fields_poll_save', data)
            .then(function(response) {
              $scope.saving = false;

              messenger.post(response.data);
            }, function(response) {
              $scope.saving = false;

              messenger.post(response.data);
            });
        };
      }
    ]);
})();
