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
    .controller('AttachmentCtrl', [
      '$controller', '$scope',
      function($controller, $scope) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

        /**
         * @memberOf AttachmentCtrl
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
          with_comment: 0,
          categories: [],
          tags: [],
          agency: ''
        };

        /**
         * @memberOf AttachmentCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          createItem: 'api_v1_backend_attachment_create_item',
          getItem:    'api_v1_backend_attachment_get_item',
          redirect:   'backend_attachment_show',
          saveItem:   'api_v1_backend_attachment_save_item',
          updateItem: 'api_v1_backend_attachment_update_item'
        };

        /**
         * @function getFileName
         * @memberOf AttachmentCtrl
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
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.localize($scope.data.item, 'item', true, [ 'path' ]);
        };

        /**
         * @function removeFile
         * @memberOf AttachmentCtrl
         *
         * @description
         *   Removes the file.
         */
        $scope.removeFile = function() {
          $scope.item.path = null;
        };

        /**
         * @inheritdoc
         */
        $scope.validate = function() {
          if (!$('[name=form]')[0].checkValidity() || !$scope.item.path) {
            $('[name=form]')[0].reportValidity();
            return false;
          }

          return true;
        };

        // Update path in original item when localized item changes
        $scope.$watch('item.path', function(nv) {
          if ($scope.data && $scope.data.item) {
            $scope.data.item.path = nv;
          }
        });
      }
    ]);
})();
