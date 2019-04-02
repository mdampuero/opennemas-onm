/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('ContentRestInnerCtrl', [
  '$controller', '$http', '$uibModal', '$rootScope', '$scope', 'routing', '$timeout',
  function($controller, $http, $uibModal, $rootScope, $scope, routing, $timeout) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

    /**
     * @function getItemId
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *   Returns the item id.
     *
     * @return {Integer} The item id.
     */
    $scope.getItemId = function() {
      return $scope.item.pk_content;
    };

    // Update slug when title is updated
    $scope.$watch('item.title', function(nv, ov) {
      if (!nv) {
        return;
      }

      if (!$scope.item.slug || $scope.item.slug === '') {
        if ($scope.tm) {
          $timeout.cancel($scope.tm);
        }

        $scope.tm = $timeout(function() {
          $scope.getSlug(nv, function(response) {
            $scope.item.slug = response.data.slug;
          });
        }, 2500);
      }
    }, true);
  }
]);
