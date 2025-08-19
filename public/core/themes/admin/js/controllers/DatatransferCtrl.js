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
      '$controller', '$scope', '$http', 'messenger', 'routing', 'oqlEncoder', '$window',
      function($controller, $scope, $http, messenger, routing, oqlEncoder, $window) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @ngdoc property
         * @name DatatransferCtrl#availableColumns
         * @propertyOf DatatransferCtrl
         * @type {Object}
         * @description
         * Configuration for available columns by entity type. Each entity type (e.g., `widget`, `advertisement`)
         * contains a name and a list of column definitions, each with internal `name` and user-facing `display` values.
         */
        $scope.availableColumns = {
          widget: {
            name: 'widget',
            columns: [
              { name: 'widget_type', display: 'Type', with: 30 },
              { name: 'title', display: 'Title' },
              { name: 'class', display: 'Content' }
            ]
          },
          advertisement: {
            name: 'advertisement',
            columns: [
              { name: 'title', display: 'Title' },
              { name: 'advertisements.0.with_script', display: 'Type', with: 30 },
            ]
          }
        };

        /**
         * @ngdoc property
         * @name DatatransferCtrl#template
         * @propertyOf DatatransferCtrl
         * @type {Object}
         * @description
         * Object holding file reference used during import.
         */
        $scope.template = {
          file: null
        };

        /**
         * @ngdoc property
         * @name DatatransferCtrl#routes
         * @propertyOf DatatransferCtrl
         * @type {Object}
         * @description
         * Set of API endpoint routes used in the data transfer process.
         */
        $scope.routes = {
          importItem: 'api_v1_backend_datatransfer_import'
        };

        /**
         * @ngdoc property
         * @name DatatransferCtrl#routes
         * @propertyOf DatatransferCtrl
         * @type {Object}
         * @description
         * Control pagination for loadTable
         */
        $scope.pagination = {
          currentPage: 1,
          itemsPerPage: 15,
        };

        /**
         * @ngdoc getPaginatedItems
         * @name DatatransferCtrl#getPaginatedItems
         * @methodOf DatatransferCtrl
         * @description
         * Obtain the paginated Items
         */
        $scope.getPaginatedItems = function() {
          if (!$scope.items) {
            return [];
          }

          const start = ($scope.pagination.currentPage - 1) * $scope.pagination.itemsPerPage;
          const end = start + $scope.pagination.itemsPerPage;

          return $scope.items.slice(start, end);
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
          $scope.saving = true;
          $scope.filename = template.file.name;
          $scope.importedData = null;

          const reader = new FileReader();

          var route = {
            name: $scope.routes.importItem,
          };

          var url = routing.generate($scope.routes.importItem);

          reader.onload = function(event) {
            try {
              const json = JSON.parse(event.target.result);

              return $http.post(url, json, {
                headers: { 'Content-Type': 'application/json' },
                transformRequest: angular.toJson
              }).then(function(response) {
                $scope.saving = false;
                messenger.post(response.data);
                $scope.clearData();
              });
            } catch (e) {
              $scope.saving = false;
              $scope.clearData();
              messenger.post($window.strings.forms.not_valid, 'error');
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
          $scope.pagination.currentPage = 1;

          const reader = new FileReader();

          reader.onload = function(event) {
            try {
              const content = event.target.result;
              const parsedData = JSON.parse(content);

              const contentType = parsedData.metadata.content_type;

              if (contentType && $scope.availableColumns[contentType]) {
                $scope.displayedColumns = $scope.availableColumns[contentType].columns;
              } else {
                $scope.displayedColumns = Object.keys(parsedData.items || {});
              }

              $scope.importedData = parsedData;
              $scope.items = parsedData.items;
              $scope.totalItems = parsedData.items.length;

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

          // Reset the file input element to allow re-uploading the same file
          const input = document.getElementById('file');

          if (input) {
            input.value = null;
          }
        };

        $scope.getNestedValue = function(obj, path) {
          return path.split('.').reduce(function(o, k) {
            return o ? o[k] : null;
          }, obj);
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
