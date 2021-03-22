/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('ContentRestInnerCtrl', [
  '$controller', '$http', '$uibModal', '$rootScope', '$scope', 'cleaner',
  'messenger', 'routing', '$timeout',
  function($controller, $http, $uibModal, $rootScope, $scope, cleaner,
      messenger, routing, $timeout) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

    /**
     * @inheritdoc
     */
    $scope.getData = function() {
      var data = angular.extend({}, $scope.data.item);

      return cleaner.clean(data);
    };

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
     * @inheritdoc
     */
    $scope.hasMultilanguage = function() {
      return $scope.config && $scope.config.locale &&
        $scope.config.locale.multilanguage;
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
        messenger.post(window.strings.forms.not_valid, 'error');
        return;
      }

      $scope.flags.http.saving = true;

      $scope.$broadcast('onmTagsInput.save', {
        onError: $scope.errorCb,
        onSuccess: function(ids) {
          $scope.item.tags      = ids;
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
      if ($scope.form && $scope.form.$invalid) {
        $('[name=form]')[0].reportValidity();
        return false;
      }

      if (!$('[name=form]')[0].checkValidity()) {
        $('[name=form]')[0].reportValidity();
        return false;
      }

      return true;
    };

    // Generates slug when flag changes
    $scope.$watch('flags.generate.slug', function(nv) {
      if ($scope.item.slug || !nv || !$scope.item.title) {
        $scope.flags.generate.slug = false;

        return;
      }

      if ($scope.tm) {
        $timeout.cancel($scope.tm);
      }

      $scope.tm = $timeout(function() {
        $scope.getSlug($scope.item.title, function(response) {
          $scope.item.slug           = response.data.slug;
          $scope.flags.generate.slug = false;
          $scope.flags.block.slug    = true;

          $scope.form.slug.$setDirty(true);
        });
      }, 250);
    }, true);
  }
]);
