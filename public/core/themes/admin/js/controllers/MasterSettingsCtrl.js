(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name MasterSettingsCtrl
     *
     * @requires $controller
     * @requires $scope
     *
     * @description
     * Controller responsible for managing the master settings with CodeMirror editors.
     */
    .controller('MasterSettingsCtrl', [
      '$controller', '$scope',
      function($controller, $scope) {
        // Inherit from the SettingsCtrl controller
        $.extend(this, $controller('SettingsCtrl', { $scope: $scope }));

        // Initialize settings with default values
        $scope.settings = {
          theme_editor: 'default',
          gfk: {}
        };

        // API routes for saving and retrieving configuration
        $scope.routes = {
          saveConfig: 'api_v1_backend_settings_master_save',
          getConfig: 'api_v1_backend_settings_master_list'
        };

        // Store CodeMirror editor instances keyed by their IDs
        $scope.editors = {};

        /**
         * @ngdoc method
         * @name initializeEditor
         * @methodOf MasterSettingsCtrl
         *
         * @description
         * Initializes CodeMirror editors for all textareas identified by their IDs.
         * Updates the corresponding settings and sets up a watcher for content changes.
         */
        $scope.initializeEditor = function() {
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

          // Initialize $scope.editors
          $scope.editors = {};

          selectors.forEach(function(selector) {
            var textarea = document.getElementById(selector.id);

            if (textarea) {
              // Create a new CodeMirror instance
              var editor = CodeMirror.fromTextArea(textarea, {
                autoCloseBrackets: true,
                lineNumbers: true,
                lineWrapping: true,
                matchBrackets: true,
                mode: 'htmlmixed',
                theme: $scope.settings.theme_editor
              });

              // Store the editor instance in $scope.editors
              $scope.editors[selector.id] = editor;

              // Set the initial value from $scope.settings
              editor.setValue($scope.settings[selector.key]);

              // Update $scope.settings when the editor content changes
              editor.on('change', function() {
                $scope.$apply(function() {
                  $scope.settings[selector.key] = editor.getValue();
                });
              });
            }
          });
        };

        /**
         * @ngdoc method
         * @name toggleAllEditorsTheme
         * @methodOf MasterSettingsCtrl
         *
         * @description
         * Toggles the theme of all CodeMirror editors between 'default' and 'material-palenight'.
         */
        $scope.toggleAllEditorsTheme = function() {
          // Detect if any editor is using 'material-palenight' theme
          const isAnyPalenight = Object.values($scope.editors).some((editor) => editor.getOption('theme') === 'material-palenight');

          // Toggle theme based on the current state
          const newTheme = isAnyPalenight ? $scope.settings.theme_editor : 'material-palenight';

          // Apply the new theme to all editors
          Object.values($scope.editors).forEach((editor) => editor.setOption('theme', newTheme));
        };

        // Watch for the presence of all required elements in the DOM before initializing editors
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
              $scope.initializeEditor();
            }
          }
        );
      }
    ]);
})();
