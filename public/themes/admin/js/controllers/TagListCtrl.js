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
          delete:         'api_v1_backend_tag_delete',
          deleteSelected: 'api_v1_backend_tag_delete',
          update:         'api_v1_backend_tag_update',
          save:           'api_v1_backend_tag_save',
          list:           'api_v1_backend_tags_list',
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

          oqlEncoder.configure({ placeholder: { name: '[key] ~ "[value]"' } });
          $scope.list();
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
