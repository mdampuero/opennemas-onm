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
        $scope.extra  = {};
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
            $scope.extra  = response.data.extra;
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

          $scope.cleanKeys();

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
         *   Validate for $scope.config elements
         */
        $scope.validate = function() {
          var validKeys = {
            onm: [
              'comment_system', 'disable_comments', 'with_comments',
              'number_elements' ],
            facebook: [ 'comment_system', 'disable_comments', 'with_comments',
              'facebook_apikey' ],
            disqus: [ 'comment_system', 'disable_comments', 'with_comments',
              'disqus_secretkey', 'disqus_shortname' ]
          };

          for (var key in validKeys[$scope.config.comment_system]) {
            var element = validKeys[$scope.config.comment_system][key];

            if ($scope.config[element] === null || $scope.config[element].length < 1) {
              return false;
            }
          }

          return true;
        };

        /**
         * @function cleanKeys
         * @memberOf CommentsConfigCtrl
         *
         * @description
         *   Clean $scope.config keys for saving data
         */
        $scope.cleanKeys = function() {
          var validKeys = {
            onm: [
              'acton_list', 'comment_system', 'disable_comments', 'with_comments',
              'number_elements', 'required_email', 'moderation_manual',
              'moderation_autoaccept', 'moderation_autoreject'
            ],
            facebook: [ 'comment_system', 'disable_comments', 'with_comments', 'facebook_apikey' ],
            disqus: [ 'comment_system', 'disable_comments', 'with_comments', 'disqus_secretkey', 'disqus_shortname' ]
          };

          Object.keys($scope.config).forEach((key) => {
            if (!validKeys[$scope.config.comment_system].includes(key)) {
              delete $scope.config[key];
            }
          });
        };
      }
    ]);
})();
