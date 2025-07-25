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

        /**
         * @ngdoc Import function
         * @name  DatatransferCtrl#import
         * @methodOf DatatransferCtrl
         * @description
         *   Imports data from a file.
         * @param {File} file The file to import.
         */
        $scope.import = function(template) {
          if (!template.file) {
            messenger.error('Please select a file to import.');
            return;
          }

          const reader = new FileReader();

          reader.onload = function(event) {
            try {
              const json = JSON.parse(event.target.result);

              $scope.$apply(function() {
                $scope.importedData = json;
              });
            } catch (e) {
              messenger.error('Error parsing JSON: ' + e.message);
            }
          };

          reader.onerror = function(event) {
            messenger.error('Error reading file: ' + event.target.error.message);
          };

          reader.readAsText(template.file);
        };
      }
    ]);
})();
