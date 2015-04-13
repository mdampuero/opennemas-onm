/**
 * Handle actions for widget inner form.
 */
angular.module('BackendApp.controllers').controller('WidgetCtrl', [
  '$controller', '$rootScope', '$scope',
  function($controller, $rootScope, $scope) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * Adds an empty parameter to the parameters list
     *
     * @param Object answers The poll answers.
     */
    $scope.addParameter = function () {
      $scope.params.push({name: '', value: ''});
    };

    /**
     * Removes a parameter from the list given its index
     *
     * @param int the parameter index in the list of parameters
     */
    $scope.removeParameter = function (index) {
      $scope.params.splice(index, 1);
    };

    /**
     * Parse the params from template and initialize the scope properly
     *
     * @param Object params The widget params.
     */
    $scope.parseParams = function(params) {
      console.log(params);
      if (params == null) {
        $scope.params = [];
      } else {
        $scope.params = params;
      }
    };

    /**
     * Updates internal parsedParameters parameter when parameters change.
     *
     * @param Object nv The new values.
     * @param Object ov The old values.
     */
    $scope.$watch('params', function(nv, ov) {
      if (nv === ov) {
        return false;
      }

      $scope.parsedParams = [];
      for (var i = $scope.params.length - 1; i >= 0; i--) {
        $scope.parsedParams.push({name: $scope.params[i].name, value: $scope.params[i].value});
      }

      $scope.parsedParams = JSON.stringify($scope.parsedParams.reverse());
    }, true);
  }
]);
