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
      '$controller', '$scope', 'oqlEncoder', 'http', 'messenger',
      function($controller, $scope, oqlEncoder, http, messenger) {
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
          deleteItem: 'api_v1_backend_onmai_prompt_delete_item',
          deleteList: 'api_v1_backend_onmai_prompt_delete_list',
          getList:    'api_v1_backend_onmai_prompt_get_list',
          saveItem:   'api_v1_backend_onmai_prompt_save_item',
          updateItem: 'api_v1_backend_onmai_prompt_update_item',
          getConfig: 'api_v1_backend_onmai_get_config',
          saveConfig: 'api_v1_backend_onmai_save_config',
          uploadConfig: 'api_v1_backend_onmai_upload_config',
          downloadConfig: 'api_v1_backend_onmai_download_config',
        };

        $scope.criteria = {
          epp: 200,
          page: 1,
          orderBy: { name: 'asc' }
        };

        /**
         * @function init
         * @memberOf PromptListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.app.columns.selected = _.uniq(
            $scope.app.columns.selected.concat([ 'name', 'role', 'mode' ])
          );

          oqlEncoder.configure({ placeholder: {
            name: 'name ~ "%[value]%"',
          } });

          $scope.list();
        };

        $scope.isSelectable = function() {
          return $scope.routes.getList === 'api_v1_backend_onmai_prompt_get_list';
        };

        $scope.$watch('routes.getList', function(nv, ov) {
          if (!ov || nv === ov) {
            return;
          }
          $scope.list();
        });

        /**
         * @function save
         * @memberOf OnmAIConfigCtrl
         *
         * @description
         *   Saves the configuration.
         */
        $scope.save = function() {
          if (!$scope.flags.http.checking) {
            $scope.flags.http.saving = true;
          }

          var data = $scope.data.extra;

          return http.put($scope.routes.saveConfig, data)
            .then(function(response) {
              if (!$scope.flags.http.checking) {
                $scope.disableFlags('http');
                messenger.post(response.data);
              }
            }, function(response) {
              if (!$scope.flags.http.checking) {
                $scope.disableFlags('http');
                messenger.post(response.data);
              }
            });
        };

        $scope.addRole = function() {
          const role = {
            name: '',
            prompt: ''
          };

          $scope.data.extra.roles.push(role);
        };

        $scope.removeRole = function(index) {
          $scope.data.extra.roles.splice(index, 1);
        };

        $scope.addTone = function() {
          const tone = {
            name: '',
            description: ''
          };

          $scope.data.extra.tones.push(tone);
        };

        $scope.removeTone = function(index) {
          $scope.data.extra.tones.splice(index, 1);
        };
      }
    ]);
})();
