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
      '$controller', '$scope', 'http', 'messenger', 'routing', 'oqlEncoder',
      function($controller, $scope, http, messenger, routing, oqlEncoder) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @ngdoc available types import
         * @name  DatatransferCtrl#availableTypes
         * @propertyOf DatatransferCtrl
         * @type    {Array}
         * @description
         *   List of available types for data transfer.
         */
        $scope.availableTypes = [
          { name: 'json', mymetype: 'application/json' },
          { name: 'csv', mymetype: 'text/csv' },
        ];

        $scope.previewError = false;

        /**
         * @ngdoc Import function
         * @name DatatransferCtrl#import
         * @methodOf DatatransferCtrl
         * @description
         * Imports data from a file.
         * @param {File} file The file to import.
         */
        $scope.import = function(template) {
          if (!template.file) {
            messenger.error('Please select a file to import.');
            return;
          }

          // Limpiar estados previos
          $scope.filename = template.file.name;
          $scope.previewError = null;
          $scope.importedData = null;

          const reader = new FileReader();

          reader.onload = function(event) {
            try {
              const json = JSON.parse(event.target.result);

              // Aplicar cambios en el scope de Angular
              $scope.$apply(function() {
                $scope.importedData = json;
                $scope.previewError = null;
              });
            } catch (e) {
              $scope.$apply(function() {
                $scope.previewError = 'Error parsing JSON: ' + e.message;
                $scope.importedData = null;
              });
            }
          };

          reader.onerror = function(event) {
            $scope.$apply(function() {
              $scope.previewError = 'Error reading file: ' + (event.target.error ? event.target.error.message : 'Unknown error');
              $scope.importedData = null;
            });
          };

          const timeoutId = setTimeout(function() {
            $scope.$apply(function() {
              $scope.previewError = 'File processing timeout. The file might be too large.';
              $scope.importedData = null;
            });
          }, 10000);

          const originalOnLoad = reader.onload;
          const originalOnError = reader.onerror;

          reader.onload = function(event) {
            clearTimeout(timeoutId);
            originalOnLoad.call(this, event);
          };

          reader.onerror = function(event) {
            clearTimeout(timeoutId);
            originalOnError.call(this, event);
          };

          // Iniciar lectura del archivo
          reader.readAsText(template.file);
        };
      }
    ]);
})();
