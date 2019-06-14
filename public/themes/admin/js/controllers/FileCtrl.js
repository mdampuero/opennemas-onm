(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  fileCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $uibModal
     * @requires linker
     * @requires localizer
     * @requires messenger
     * @requires routing
     */
    .controller('FileCtrl', [
      '$controller', '$scope',
      function($controller, $scope) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf FileCtrl
         *
         * @description
         *  The item object.
         *
         * @type {Object}
         */
        $scope.item = {
          body: '',
          content_type_name: 'attachment',
          fk_content_type: 3,
          content_status: 0,
          description: '',
          favorite: 0,
          frontpage: 0,
          created: new Date(),
          starttime: null,
          endtime: null,
          title: '',
          type: 0,
          with_comments: 0,
          categories: [],
          tags: [],
          agency: '',
        };

        /**
         * @memberOf FileCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          create:   'api_v1_backend_file_create',
          redirect: 'backend_file_show',
          save:     'api_v1_backend_file_save',
          show:     'api_v1_backend_file_show',
          update:   'api_v1_backend_file_update'
        };

        /**
         * @function getFileName
         * @memberOf FileCtrl
         *
         * @description
         *   Returns the filename for a File or a string.
         *
         * @return {String} The filename.
         */
        $scope.getFileName = function() {
          if (!$scope.item.path) {
            return '';
          }

          if (angular.isObject($scope.item.path)) {
            return $scope.item.path.name;
          }

          return $scope.item.path.replace(/.*\/([^/]+)/, '$1');
        };

        /**
         * @function removeFile
         * @memberOf FileCtrl
         *
         * @description
         *   Removes the file.
         */
        $scope.removeFile = function() {
          $scope.item.path = null;
        };
      }
    ]);
})();
