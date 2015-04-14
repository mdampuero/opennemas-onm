angular.module('BackendApp.controllers')
  /**
  * @ngdoc controller
  * @name  WidgetCtrl
  *
  * @description
  *   Handles actions in widget inner.
  *
  * @requires $controller
  * @requires $rootScope
  * @requires $scope
  */
  .controller('WidgetCtrl', ['$controller', '$rootScope', '$scope',
    function($controller, $rootScope, $scope) {
      'use strict';

      // Initialize the super class and extend it.
      $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

      /**
       * @function addParameter
       * @memberOf WidgetCtrl
       *
       * @description
       *   Adds an empty parameter to the parameters list
       *
       * @param {Object} answers The poll answers.
       */
      $scope.addParameter = function () {
        $scope.params.push({name: '', value: ''});
      };

      /**
       * @function parseParams
       * @memberOf WidgetCtrl
       *
       * @description
       *   Parse the params from template and initialize the scope properly
       *
       * @param Object params The widget params.
       */
      $scope.parseParams = function(params) {
        if (params == null) {
          $scope.params = [];
        } else {
          $scope.params = params;
        }
      };

      /**
       * @function removeParameter
       * @memberOf WidgetCtrl
       *
       * @description
       *   Removes a parameter from the list given its index
       *
       * @param {Integer} the parameter index in the list of parameters
       */
      $scope.removeParameter = function (index) {
        $scope.params.splice(index, 1);
      };

      // Updates internal parsedParameters parameter when parameters change.
      $scope.$watch('params', function() {
        $scope.parsedParams = [];

        for (var i = $scope.params.length - 1; i >= 0; i--) {
          $scope.parsedParams.push({name: $scope.params[i].name, value: $scope.params[i].value});
        }

        $scope.parsedParams = JSON.stringify($scope.parsedParams.reverse());
      }, true);
    }
  ]);
