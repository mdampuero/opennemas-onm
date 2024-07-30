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
        $.extend(this, $controller('SettingsCtrl', { $scope: $scope }));

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

        // Use a $watch to wait for the DOM to be ready
        $scope.$watch(
          function() {
            // Check if all required elements are present in the DOM
            return document.getElementById('header-script') &&
                   document.getElementById('body-start-script') &&
                   document.getElementById('body-end-script') &&
                   document.getElementById('header-script-amp') &&
                   document.getElementById('body-start-script-amp') &&
                   document.getElementById('body-end-script-amp') &&
                   document.getElementById('custom-css-amp');
          },
          function(newVal) {
            if (newVal) {
              var selectors = [
                { id: 'header-script', key: 'header_script' },
                { id: 'body-start-script', key: 'body_start_script' },
                { id: 'body-end-script', key: 'body_end_script' },
                { id: 'header-script-amp', key: 'header_script_amp' },
                { id: 'body-start-script-amp', key: 'body_start_script_amp' },
                { id: 'body-end-script-amp', key: 'body_end_script_amp' },
                { id: 'custom-css-amp', key: 'custom_css_amp' }
              ];

              // Initialize $scope.settings with empty values if they don't exist
              selectors.forEach(function(element) {
                if (!$scope.settings[element.key]) {
                  $scope.settings[element.key] = '';
                }
              });

              // Iterate over each selector and configure CodeMirror
              selectors.forEach(function(selector) {
                var textarea = document.getElementById(selector.id);

                if (textarea) {
                  var editor = CodeMirror.fromTextArea(textarea, {
                    mode: 'htmlmixed',
                    theme: 'dracula',
                    lineNumbers: true,
                    matchBrackets: true,
                    autoCloseBrackets: true
                  });

                  // Set the initial value of the editor from $scope.settings
                  editor.setValue($scope.settings[selector.key]);

                  // Update $scope.settings when the editor content changes
                  editor.on('change', function() {
                    $scope.$apply(function() {
                      $scope.settings[selector.key] = editor.getValue();
                    });
                  });
                }
              });
            }
          }
        );
      }
    ]);
})();
