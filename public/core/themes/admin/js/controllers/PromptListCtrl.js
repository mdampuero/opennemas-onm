(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  PromptListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Handles all actions in tag list.
     */
    .controller('PromptListCtrl', [
      '$controller', '$scope', 'oqlEncoder',
      function($controller, $scope, oqlEncoder) {
        $.extend(this, $controller('RestListCtrl', { $scope: $scope }));

        /**
         * @memberOf PromptListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_openai_prompt_delete_item',
          deleteList: 'api_v1_backend_openai_prompt_delete_list',
          getList:    'api_v1_backend_openai_prompt_get_list',
          saveItem:   'api_v1_backend_openai_prompt_save_item',
          updateItem: 'api_v1_backend_openai_prompt_update_item',
        };

        /**
         * @function init
         * @memberOf PromptListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.app.columns.selected = _.uniq([
            ... $scope.app.columns.selected,
            'name',
            'field',
            'context'
          ]);

          oqlEncoder.configure({ placeholder: {
            name: 'name ~ "%[value]%"',
          } });

          $scope.list();
        };
      }
    ]);
})();
