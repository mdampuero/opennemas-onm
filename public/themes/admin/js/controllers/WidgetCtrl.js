/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('WidgetCtrl', [
  '$controller', '$rootScope', '$scope',
  function($controller, $rootScope, $scope) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    $scope.addParameter = function () {
      $scope.params.push({name: '', value: ''});
    };

    $scope.removeParameter = function (index) {
      $scope.params.splice(index, 1);
    };

    /**
     * Parse the params from template and initialize the scope properly
     *
     * @param Object params The album params.
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
     * Updates the ids and footers when params change.
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
