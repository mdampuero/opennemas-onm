/**
 * Handle actions for cache config form.
 */
angular.module('BackendApp.controllers').controller('CacheConfigCtrl', [
  '$controller', '$rootScope', '$scope',
  function($controller, $rootScope, $scope) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * The list of selected elements.
     *
     * @type array
     */
    $scope.selected = {
      all: false,
      contents: []
    };

    /**
     * Initialize $scope from view and populate selected contents
     *
     * @type array
     */
    $scope.init = function(config) {
        $scope.config = config;
        // Populate selected contents
        angular.forEach($scope.config, function(value, key) {
          if(value.caching == 1) {
            this.push(key);
          }
        }, $scope.selected.contents);
        // Check selectAll if all items are selected
        if ($scope.selected.contents.length == Object.keys($scope.config).length) {
          $scope.selected.all = true;
        };
    }

    /**
     * Selects/unselects all instances.
     */
    $scope.selectAll = function() {
      if ($scope.selected.all) {
        $scope.selected.contents = Object.keys($scope.config);
      } else {
        $scope.selected.contents = [];
      }
    };
  }
]);
