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

    /**
     * @function submit
     * @memberOf StaticPageCtrl
     *
     * @description
     *   Saves tags and, then, saves the item.
     */
    $scope.submit = function() {
      if (!$scope.validate()) {
        return;
      }

      $scope.flags.http.saving = true;

      $scope.$broadcast('onmTagsInput.save', {
        onError: $scope.errorCb,
        onSuccess: function(ids) {
          $scope.data.item.tags = ids;
          $scope.save();
        }
      });
    };

    /**
     * @function validate
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *   Validates the form and/or the current item in the scope.
     *
     * @return {Boolean} True if the form and/or the item are valid. False
     *                   otherwise.
     */
    $scope.validate = function() {
      if (!$('[name=form]')[0].checkValidity()) {
        $('[name=form]')[0].reportValidity();
        return false;
      }

      return true;
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
