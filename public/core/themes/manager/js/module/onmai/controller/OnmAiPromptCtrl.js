(function() {
  'use strict';

  angular.module('ManagerApp.controllers')

    /**
     * @ngdoc controller
     * @name  OnmAiPromptCtrl
     *
     * @requires $filter
     * @requires $location
     * @requires $scope
     * @requires http
     * @requires routing
     * @requires messenger
     * @requires data
     *
     * @description
     *   Handles actions for instance edition form
     */
    .controller('OnmAiPromptCtrl', [
      '$location', '$routeParams', '$scope', 'http', 'routing', 'messenger', '$timeout',
      function($location, $routeParams, $scope, http, routing, messenger, $timeout) {
        /**
         * @memberOf OnmAiPromptCtrl
         *
         * @description
         *  The client object.
         *
         * @type {Object}
         */
        $scope.item = {
          field: 'titles',
          mode: 'New',
          instances: [ 'Todos' ],
          instructions: [],
        };

        $scope.instructionsAvailables = [];
        $scope.previewTimeout         = null;
        $scope.filterRole             = $scope.item.field;

        /**
         * @function save
         * @memberOf OnmAiPromptCtrl
         *
         * @description
         *   Saves the client.
         */
        $scope.save = function() {
          $scope.saving = 1;
          var data = angular.copy($scope.item);

          if (data.instances) {
            if (data.instances.length > 0) {
              data.instances = data.instances.map(function(a) {
                return a.name;
              });
            } else {
              data.instances = [ 'Todos' ];
            }
          }
          http.post('manager_ws_onmai_prompt_save', data)
            .then(function(response) {
              messenger.post(response.data);

              if (response.status === 201) {
                var url = response.headers().location.replace('/managerws', '');

                $location.path(url);
              }
            }, function(response) {
              messenger.post(response.data);
              $scope.saving = 0;
            });
        };

        /**
         * @function autocomplete
         * @memberOf NotificationCtrl
         *
         * @description
         *   Suggest a list of instance basing on the current query.
         *
         * @return {Array} A list of targets
         */
        $scope.autocomplete = function(query) {
          var route = {
            name: 'manager_ws_onmai_prompt_autocomplete',
            params: { query: query }
          };

          return http.get(route).then(function(response) {
            return response.data.target;
          });
        };

        /**
         * @function update
         * @memberOf container
         *
         * @description
         *   Updates a item.
         */
        $scope.update = function() {
          $scope.saving = 1;

          var data = angular.copy($scope.item);

          if (data.instances) {
            if (data.instances.length > 0) {
              data.instances = data.instances.map(function(a) {
                return a.name;
              });
            } else {
              data.instances = [ 'Todos' ];
            }
          }
          var route = {
            name: 'manager_ws_onmai_prompt_update',
            params: { id: $scope.item.id }
          };

          http.put(route, data)
            .then(function(response) {
              messenger.post(response.data);
              $scope.saving = 0;
            }, function(response) {
              messenger.post(response.data);
              $scope.saving = 0;
            });
        };

        $scope.generatePreview = function() {
          var data = {
            item: $scope.item,
          };

          http.post('manager_ws_onmai_prompt_preview', data)
            .then(function(response) {
              $scope.preview = response.data.promptPreview;
            });
        };

        $scope.$on('$destroy', function() {
          $scope.item = null;
        });

        var route = 'manager_ws_onmai_prompt_new';

        if ($routeParams.id) {
          route = {
            name: 'manager_ws_onmai_prompt_show',
            params: { id: $routeParams.id }
          };
        }

        http.get(route).then(function(response) {
          $scope.extra = response.data.extra;
          if (response.data.item) {
            $scope.item = response.data.item;
          }

          for (var i = 0; i < $scope.extra.onmai_instructions.length; i++) {
            var instructionId = $scope.extra.onmai_instructions[i].id;

            if ($scope.item.instructions.indexOf(instructionId) === -1) {
              $scope.instructionsAvailables.push(instructionId);
            }
          }
          $scope.generatePreview();
        });

        $scope.$watch('item.field', function(nv, ov) {
          if (!nv || nv === ov) {
            return;
          }
          $scope.filterRole = $scope.item.field;
        }, true);

        $scope.selectInstruction = function(item) {
          const index = $scope.instructionsAvailables.indexOf(item);

          if (index !== -1) {
            $scope.instructionsAvailables.splice(index, 1);
            $scope.item.instructions.push(item);
          }
          $scope.generatePreview();
        };

        $scope.deselectInstruction = function(item) {
          const index = $scope.item.instructions.indexOf(item);

          if (index !== -1) {
            $scope.item.instructions.splice(index, 1);
            $scope.instructionsAvailables.push(item);
          }
          $scope.generatePreview();
        };

        $scope.getInstruction = function(id) {
          for (var i = 0; i < $scope.extra.onmai_instructions.length; i++) {
            var instruction = $scope.extra.onmai_instructions[i];

            if (instruction.id === id) {
              return instruction;
            }
          }
          return null;
        };

        $scope.filterInstructions = function(id) {
          if (!$scope.searchText) {
            return true;
          }
          var title = $scope.getInstructionTitle(id);

          return title && title.toLowerCase().indexOf($scope.searchText.toLowerCase()) !== -1;
        };

        $scope.getInstructionTitle = function(id) {
          for (var i = 0; i < $scope.extra.onmai_instructions.length; i++) {
            var instruction = $scope.extra.onmai_instructions[i];

            if (instruction.id === id) {
              return instruction.title;
            }
          }
          return id;
        };

        $scope.hasTitle = function(item) {
          return item.title && item.title.trim() !== '';
        };

        $scope.delayedPreview = function() {
          if ($scope.previewTimeout) {
            $timeout.cancel($scope.previewTimeout);
          }

          $scope.previewTimeout = $timeout(function() {
            $scope.generatePreview();
          }, 1000);
        };

        $scope.getInstructions = function() {
          var string = '{Instrucciones}\n';
          var countInstructions = 0;

          if ($scope.item.instructions.length > 0) {
            string = '';
          }
          for (var i = 0; i < $scope.item.instructions.length; i++) {
            var instruccion = $scope.item.instructions[i];

            if (instruccion.value) {
              countInstructions++;
              string += countInstructions + '. ' + instruccion.value + "\n";
            }
          }

          return string;
        };
      }
    ]);
})();

