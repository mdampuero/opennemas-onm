(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  PromptCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $timeout
     *
     * @description
     *   Handles actions for tag edit form.
     */
    .controller('PromptCtrl', [
      '$controller', '$scope', '$timeout', 'http', 'messenger',
      function($controller, $scope) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        /**
         * @inheritdoc
         */
        $scope.routes = {
          createItem:   'api_v1_backend_onmai_prompt_create_item',
          getItem:      'api_v1_backend_onmai_prompt_get_item',
          list:         'backend_onmai_prompts_list',
          redirect:     'backend_onmai_prompts_list',
          saveItem:     'api_v1_backend_onmai_prompt_save_item',
          updateItem:   'api_v1_backend_onmai_prompt_update_item'
        };
      }
    ]);
})();
