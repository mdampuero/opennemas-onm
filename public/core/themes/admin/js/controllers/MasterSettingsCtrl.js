(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  MasterSettingsCtrl
     *
     * @requires $controller
     * @requires $scope
     */
    .controller('MasterSettingsCtrl', [
      '$controller', '$scope', '$timeout',
      function($controller, $scope, $timeout) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('SettingsCtrl', { $scope: $scope, $timeout: $timeout}));

        /**
         * @memberOf MasterSettingsCtrl
         *
         * @description
         *  The settings object with default values.
         *
         * @type {Object}
         */
        $scope.settings = {
          theme_skin: 'default',
          gfk: {}
        };

        $scope.routes = {
          saveConfig: 'api_v1_backend_settings_master_save',
          getConfig: 'api_v1_backend_settings_master_list'
        };

        $timeout(function() {
          var textarea = document.getElementById('header-script');

          if (textarea) {
            var editor = CodeMirror.fromTextArea(textarea, {
              mode: 'html',
              theme: 'dracula',
              lineNumbers: true,
              matchBrackets: true,
              autoCloseBrackets: true
            });

            editor.setValue($scope.settings.header_script);

            editor.on('change', function() {
              $scope.$apply(function() {
                $scope.settings.header_script = editor.getValue();
              });
            });
          }
        }, 1000);
      }
    ]);
})();
