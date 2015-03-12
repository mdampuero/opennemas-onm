/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('MenuCtrl', [
  '$controller', '$http', '$modal', '$rootScope', '$scope', 'routing',
  function($controller, $http, $modal, $rootScope, $scope, routing) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));
  }
]);
