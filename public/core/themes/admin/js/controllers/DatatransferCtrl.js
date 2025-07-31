(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  DatatransferCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     * @requires messenger
     *
     * @description
     *   Provides actions to list notifications.
     */
    .controller('DatatransferCtrl', [
      '$controller', '$scope', 'http', 'messenger', 'routing', 'oqlEncoder', '$window',
      function($controller, $scope, http, messenger, routing, oqlEncoder, $window) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @ngdoc available columns configuration
         * @name DatatransferCtrl#availableColumns
         * @propertyOf DatatransferCtrl
         * @type {Array}
         * @description
         * Configuration for available columns by entity type.
         */
        $scope.availableColumns = {
          widget: {
            name: 'widget',
            columns: [
              { name: 'widget_type', display: 'Type' },
              { name: 'title', display: 'Title' },
              { name: 'class', display: 'Content' }
            ]
          },
          advertisement: {
            name: 'advertisement',
            columns: [
              { name: 'title', display: 'Title' },
              { name: 'position', display: 'Position' },
            ]
          }
        };

        $scope.template = {
          file: null
        };

        $scope.routes = {
          importItem: 'api_v1_backend_datatransfer_import'
        };

        /**
         * @ngdoc Import function
         * @name DatatransferCtrl#import
         * @methodOf DatatransferCtrl
         * @description
         * Imports data from a file.
         * @param {File} file The file to import.
         */
        $scope.import = function(template) {
          $scope.filename = template.file.name;
          $scope.importedData = null;

          const reader = new FileReader();
          var route = {
            name: $scope.routes.importItem,
          };

          reader.onload = function(event) {
            try {
              const json = JSON.parse(event.target.result);

              return http.put(route, {
                content: json,
              }).then(function(response) {
                messenger.post(response.data);
                $scope.clearData();
              });
            } catch (e) {
              messenger.post(response.data);
            }
          };

          // Iniciar lectura del archivo
          reader.readAsText(template.file);
        };

        /**
         * @ngdoc Load table data function
         * @name DatatransferCtrl#loadTableData
         * @methodOf DatatransferCtrl
         * @description
         * Loads and processes file data for table display.
         */
        $scope.loadTableData = function() {
          $scope.filename = $scope.template.file.name;

          const reader = new FileReader();

          reader.onload = function(event) {
            try {
              const content = event.target.result;
              const parsedData = JSON.parse(content);

              const contentType = parsedData.metadata.content_type;

              if (contentType && $scope.availableColumns[contentType]) {
                $scope.displayedColumns = $scope.availableColumns[contentType].columns;
              } else {
                $scope.displayedColumns = Object.keys(parsedData.items[0] || {});
              }

              $scope.importedData = parsedData;
              $scope.items = parsedData.items;

              $scope.$apply();
            } catch (error) {
              messenger.post($window.strings.not_valid, 'error');
            }
          };

          reader.readAsText($scope.template.file);
        };

        /**
         * Clears the currently loaded file and resets the display
         * @name DatatransferCtrl#$scope.clearData
         * @function
         */
        $scope.clearData = function() {
          $scope.template.file = null;
          $scope.displayedColumns = [];
          $scope.importedData = null;
        };

        /**
         * Watches for changes to the template file
         * @name DatatransferCtrl#$scope.$watch
         * @function
         * @listens $scope.template.file
         */
        $scope.$watch('template.file', function(newValue, oldValue) {
          if (newValue !== oldValue) {
            if (newValue) {
              $scope.loadTableData();
            }
          }
        });
      }
    ]);
})();
