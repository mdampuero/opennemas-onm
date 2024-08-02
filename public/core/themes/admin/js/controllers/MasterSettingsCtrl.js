(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  MasterSettingsCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $timeout
     *
     * @description
     * Controller responsible for managing the master settings with CodeMirror editors.
     */
    .controller('MasterSettingsCtrl', [
      '$controller', '$scope', '$timeout',
      function($controller, $scope, $timeout) {
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
        $scope.initializeEditor = function(id) {
          var textarea = document.getElementById(id);
          var editor = '';

          if (textarea) {
            // Create a new CodeMirror instance
            editor = CodeMirror.fromTextArea(textarea, {
              autoCloseBrackets: true,
              lineNumbers: true,
              lineWrapping: true,
              matchBrackets: true,
              mode: 'htmlmixed',
              theme: 'dracula',
              scrollbarStyle: null
            });

            // Store the editor instance in $scope.editors
            $scope.editors[id] = editor;

            // Set the initial value from $scope.settings
            editor.setValue($scope.settings[id]);

            // Update $scope.settings when the editor content changes
            editor.on('change', function() {
              $scope.$apply(function() {
                $scope.settings[id] = editor.getValue();
              });
            });

            // Initialize content visibility for this editor
            $scope.contentVisibility[id] = false;
          }
        };

        /**
         * @ngdoc method
         * @name toggleContent
         * @methodOf MasterSettingsCtrl
         *
         * @description
         * Toggles the visibility of the editor content based on its ID.
         * Initializes the editor if not already done.
         *
         * @param {string} id The ID of the editor to toggle.
         */
        $scope.toggleContent = function(id) {
          var editor = $scope.editors[id];

          // If editor is not initialized, initialize it
          if (!editor) {
            $scope.initializeEditor(id);
            editor = $scope.editors[id];
          }

          if (editor) {
            // Toggle editor mode and theme based on visibility state
            if ($scope.contentVisibility[id]) {
              editor.setOption('mode', 'htmlmixed');
              editor.setOption('theme', 'dracula');
            } else {
              editor.setOption('mode', false);
              editor.setOption('theme', 'default');
            }
            // Toggle visibility state
            $scope.contentVisibility[id] = !$scope.contentVisibility[id];
          }
        };
      }
    ]);
})();
