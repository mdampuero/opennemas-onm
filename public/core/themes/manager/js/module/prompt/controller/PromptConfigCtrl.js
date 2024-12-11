(function() {
  'use strict';

  angular.module('ManagerApp.controllers')

    /**
     * @ngdoc controller
     * @name  PromptConfigCtrl
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
    .controller('PromptConfigCtrl', [
      '$scope', 'http', 'messenger',
      function($scope, http, messenger) {
        $scope.save = function() {
          var data = $scope.settings;

          return http.put('manager_ws_prompt_config_save', data)
            .then(function(response) {
              messenger.post(response.data);
            }, function(response) {
              messenger.post(response.data);
            });
        };

        $scope.settings = {
          openai_roles: [],
          openai_tones: [],
          openai_instructions: []
        };

        $scope.addRole = function() {
          const role = {
            name: '',
            prompt: ''
          };

          $scope.settings.openai_roles.push(role);
        };

        $scope.removeRole = function(index) {
          $scope.settings.openai_roles.splice(index, 1);
        };

        $scope.addTone = function() {
          const tone = {
            name: '',
            description: ''
          };

          $scope.settings.openai_tones.push(tone);
        };

        $scope.removeTone = function(index) {
          $scope.settings.openai_tones.splice(index, 1);
        };

        $scope.addInstruction = function() {
          const instruction = {
            type: 'Both',
            value: ''
          };

          $scope.settings.openai_instructions.push(instruction);
        };

        $scope.removeInstruction = function(index) {
          $scope.settings.openai_instructions.splice(index, 1);
        };

        $scope.init = function() {
          var route = {
            name: 'manager_ws_prompt_config'
          };

          return http.get(route).then(function(response) {
            $scope.settings = response.data;
          }, function() {
            $scope.item = {};
          });
        };

        $scope.init();
      }
    ]);
})();

