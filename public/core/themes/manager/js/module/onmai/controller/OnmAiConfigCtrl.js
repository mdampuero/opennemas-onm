(function() {
  'use strict';

  angular.module('ManagerApp.controllers')

    /**
     * @ngdoc controller
     * @name  OnmAiConfigCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $timeout
     * @requires http
     * @requires messenger
     *
     * @description
     *   Handles all actions in prompt.txt listing.
     */
    .controller('OnmAiConfigCtrl', [
      '$controller', '$scope', '$timeout', 'http', 'messenger',
      function($controller, $scope, $timeout, http, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope: $scope,
          $timeout: $timeout
        }));

        $scope.currentTab = 'openai';
        $scope.allModels = [];

        $scope.addModel = function() {
          if ($scope.onmai_settings.engines[$scope.currentTab].models == '') {
            $scope.onmai_settings.engines[$scope.currentTab].models = [];
          }
          $scope.onmai_settings.engines[$scope.currentTab].models.push({
            cost_input_tokens: 0,
            cost_output_tokens: 0,
            id: '',
            sale_input_tokens: 0,
            sale_output_tokens: 0
          });
        };

        $scope.selectTab = function(currentTab) {
          $scope.currentTab = currentTab;
        };

        $scope.saveActiveItems = function() {
          return http.put('manager_ws_onmai_config_save', {
            onmai_settings: $scope.onmai_settings
          })
            .then(function(response) {
              $scope.getModels();
              messenger.post(response.data);
            }, function(response) {
              messenger.post(response.data);
            });
        };

        $scope.removeModel = function(index) {
          $scope.onmai_settings.engines[$scope.currentTab].models.splice(index, 1);
        };

        $scope.getModels = function() {
          $scope.allModels = [];
          for (var engineKey in $scope.onmai_settings.engines) {
            if ($scope.onmai_settings.engines.hasOwnProperty(engineKey)) {
              var engine = $scope.onmai_settings.engines[engineKey];

              for (var i = 0; i < engine.models.length; i++) {
                var model = angular.copy(engine.models[i]);

                $scope.allModels.push({
                  id: `${engineKey}_${model.id}`,
                  name: `${engineKey} - ${model.id}`
                });
              }
            }
          }
        };

        $scope.init = function() {
          $scope.loading = 1;
          var route = {
            name: 'manager_ws_onmai_config'
          };

          http.get(route).then(function(response) {
            $scope.onmai_settings = response.data.onmai_settings;
            $scope.loading = 0;
            $scope.getModels();
          });
        };

        // $scope.saveActiveItems = function() {
        //   const openaiModels = $scope.items.filter(function(item) {
        //     return item.active;
        //   }).map(function(item) {
        //     return {
        //       id: item.id,
        //       default: item.default,
        //       cost_input_tokens: item.cost_input_tokens,
        //       cost_output_tokens: item.cost_output_tokens,
        //       sale_input_tokens: item.sale_input_tokens,
        //       sale_output_tokens: item.sale_output_tokens
        //     };
        //   });

        //   return http.put('manager_ws_aimodel_save', {
        //     openai_models: openaiModels,
        //     openai_settings: $scope.openai_settings
        //   })
        //     .then(function(response) {
        //       messenger.post(response.data);
        //     }, function(response) {
        //       messenger.post(response.data);
        //     });
        // };

        // /**
        //  * @function list
        //  * @memberOf AimodelListCtrl
        //  *
        //  */
        // $scope.list = function() {
        //   $scope.loading = 1;
        //   var route = {
        //     name: 'manager_ws_aimodel_config'
        //   };

        //   http.get(route).then(function(response) {
        //     $scope.loading = 0;
        //     $scope.items = response.data.items;
        //     $scope.openai_models = response.data.openai_models;
        //     $scope.openai_settings = response.data.openai_settings;

        //     $scope.loadPersistedData();
        //   });
        // };

        // $scope.setDefaultItem = function(item) {
        //   $scope.items.forEach(function(i) {
        //     i.default = false;
        //   });

        //   item.default = true;
        //   $scope.defaultItem = item;
        // };

        // $scope.loadPersistedData = function() {
        //   $scope.openai_models.forEach(function(persistedItem) {
        //     var item = $scope.items.find(function(i) {
        //       return i.id === persistedItem.id;
        //     });

        //     if (item) {
        //       item.active = true;
        //       item.cost_input_tokens = persistedItem.cost_input_tokens;
        //       item.cost_output_tokens = persistedItem.cost_output_tokens;
        //       item.default = JSON.parse(persistedItem.default);
        //       item.sale_input_tokens = persistedItem.sale_input_tokens;
        //       item.sale_output_tokens = persistedItem.sale_output_tokens;
        //     }
        //   });

        //   $scope.items.sort(function(a, b) {
        //     return (b.active === true) - (a.active === true);
        //   });
        // };

        $scope.init();
      }
    ]);
})();

