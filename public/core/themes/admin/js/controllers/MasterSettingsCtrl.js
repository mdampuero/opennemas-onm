(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  MasterSettingsCtrl
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

        /**
         * @description
         * Default settings object with initial values.
         *
         * @type {Object}
         */
        $scope.settings = {
          theme_skin: 'default',
          gfk: {}
        };

        /**
         * @description
         * API routes for saving and retrieving configuration.
         *
         * @type {Object}
         */
        $scope.routes = {
          saveConfig: 'api_v1_backend_settings_master_save',
          getConfig: 'api_v1_backend_settings_master_list'
        };

        /**
         * @description
         * Store CodeMirror editor instances keyed by their IDs.
         *
         * @type {Object}
         */
        $scope.editors = {};

        /**
         * @description
         * Visibility states for each editor's content.
         *
         * @type {Object}
         */
        $scope.contentVisibility = {};

        /**
         * @ngdoc method
         * @name initializeEditor
         * @methodOf MasterSettingsCtrl
         *
         * @description
         * Initializes a CodeMirror editor for a given textarea ID.
         * Updates the corresponding settings and sets up a watcher for content changes.
         *
         * @param {string} id The ID of the textarea element.
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

          // Initialize $scope.editors and $scope.contentVisibility
          $scope.editors = {};
          $scope.contentVisibility = {};

          selectors.forEach(function(selector) {
            var textarea = document.getElementById(selector.id);
            var editor = '';

            if (textarea) {
              // Create a new CodeMirror instance
              editor = CodeMirror.fromTextArea(textarea, {
                autoCloseBrackets: true,
                lineNumbers: true,
                lineWrapping: true,
                matchBrackets: true,
                mode: 'htmlmixed',
                theme: 'default',
                scrollbarStyle: null
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
          var newTheme = 'default';

          // Check if at least one editor currently has the 'material-palenight' theme
          var anyPalenight = Object.keys($scope.editors).some(function(id) {
            return $scope.editors[id].getOption('theme') === 'material-palenight';
          });

          // If any editor has the 'material-palenight' theme, switch to 'default', otherwise to 'material-palenight'
          if (!anyPalenight) {
            newTheme = 'material-palenight';
          }

          // Update the theme for all editors
          Object.keys($scope.editors).forEach(function(id) {
            $scope.editors[id].setOption('theme', newTheme);
          });
        };

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
