(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  CommentsConfigCtrl
     *
     * @requires $controller
     * @requires $scope
     *
     * @description
     *   Provides actions to edit, save and update articles.
     */
    .controller('CommentsConfigCtrl', [
      '$controller', '$scope', 'http', 'messenger',
      function($controller, $scope, http, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf CommentsConfigCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          list:         'backend_comments_list',
          getConfig:    'api_v1_backend_comment_get_config',
          saveConfig:   'api_v1_backend_comment_save_config',
        };

        /**
         * @memberOf CommentsConfigCtrl
         *
         * @description
         *  The extraFields object.
         *
         * @type {Object}
         */
        $scope.config = {};
        $scope.extra = {};
        $scope.saving = false;

        /**
         * @function init
         * @memberOf CommentsConfigCtrl
         *
         * @description
         *   Initializes the form.
         */
        $scope.init = function() {
          $scope.list();
        };

        /**
         * @function list
         * @memberOf CommentsConfigCtrl
         *
         * @description
         *   Reloads the tag configuration.
         */
        $scope.list = function() {
          $scope.flags.http.loading = true;

          http.get($scope.routes.getConfig).then(function(response) {
            $scope.config = response.data.config;
            $scope.extra = response.data.extra;
            $scope.disableFlags('http');
          }, function() {
            $scope.disableFlags('http');
          });
        };

        /**
         * @function save
         * @memberOf CommentsConfigCtrl
         *
         * @description
         *  Saves the configuration.
         */
        $scope.save = function() {
          if (!$scope.validate()) {
            messenger.post(window.strings.forms.not_valid, 'error');
            return;
          }

          $scope.flags.http.saving = true;

          http.put($scope.routes.saveConfig, { config: $scope.config, extra: $scope.extra })
            .then(function(response) {
              $scope.disableFlags('http');
              messenger.post(response.data);
            }, function(response) {
              $scope.disableFlags('http');
              messenger.post(response.data);
            });
        };

        /**
         * @function validate
         * @memberOf CommentsConfigCtrl
         *
         * @description
         *   Validates facebook or disqus
         */
        $scope.validate = function() {
          if ($scope.extra.handler === 'facebook' &&
          (!$scope.extra.facebook.api_key ||
            !$scope.extra.facebook.api_key.trim())) {
            return false;
          }

          if ($scope.extra.handler === 'disqus' &&
            (!$scope.extra.disqus_secret_key ||
              !$scope.extra.disqus_shortname ||
              !$scope.extra.disqus_secret_key.trim() ||
              !$scope.extra.disqus_shortname.trim())) {
            return false;
          }

          if ($scope.extra.handler === 'onm' &&
            ($scope.config.number_elements < 3 ||
              $scope.config.number_elements > 100 ||
              !$scope.config.number_elements)) {
            return false;
          }

          return true;
        };
      }
    ]);
})();
