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
    /**
     * Parse the params from template and initialize the scope properly
     *
     * @param Object params The album params.
     */
    $scope.parseParams = function(params) {
      $scope.params = [];
      console.log(params);

      if (params > 0) {
        for (var i = 0; i < params.length; i++) {
          $scope.params.push({ name: params.name, value: params.value});
        }
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

      $scope.parsedParams = JSON.stringify($scope.params);
    }, true);
  }
]);
