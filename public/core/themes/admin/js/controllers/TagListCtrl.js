(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  TagListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Handles all actions in tag list.
     */
    .controller('TagListCtrl', [
      '$controller', '$scope', '$timeout', 'oqlEncoder',
      function($controller, $scope, $timeout, oqlEncoder) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @memberOf TagListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_tag_delete_item',
          deleteList: 'api_v1_backend_tag_delete_list',
          getList:    'api_v1_backend_tag_get_list',
          saveItem:   'api_v1_backend_tag_save_item',
          updateItem: 'api_v1_backend_tag_update_item',
        };

        /**
         * @function init
         * @memberOf TagListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria = $scope.criteria;
          $scope.app.columns.selected = [ 'name', 'slug', 'contents' ];
          oqlEncoder.configure({ placeholder: {
            name: 'name ~ "%[value]%" or slug ~ "%[value]%"',
          } });

          $scope.list();
        };

        /**
         * @inheritdoc
         */
        $scope.isSelectable = function() {
          return false;
        };

        /**
         * @function parseList
         * @memberOf TagListCtrl
         *
         * @description
         *   Parses the response and adds information to the scope.
         *
         * @param {Object} data The data in the response.
         */
        $scope.parseList = function(data) {
          data.extra.locales = $scope.addEmptyValue(
            $scope.toArray(data.extra.locales, 'id', 'name'));

          return data;
        };
      }
    ]);
})();
