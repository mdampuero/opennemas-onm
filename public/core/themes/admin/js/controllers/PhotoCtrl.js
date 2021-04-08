/**
 * Handle actions for image inner.
 */
angular.module('BackendApp.controllers').controller('PhotoCtrl', [
  '$controller', '$scope', '$timeout', '$uibModal', '$window', 'linker', 'localizer', 'messenger', 'routing',
  function($controller, $scope, $timeout, $uibModal, $window, linker, localizer, messenger, routing) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

    /**
     * @inheritdoc
     */
    $scope.draftEnabled = true;

    /**
     * @inheritdoc
     */
    $scope.draftKey = 'photo-draft';

    /**
     * @inheritdoc
     */
    $scope.dtm = null;

    /**
     * @memberOf PhotoCtrl
     *
     * @description
     *  The list of routes for the controller.
     *
     * @type {Object}
     */
    $scope.routes = {
      getItem:    'api_v1_backend_photo_get_item',
      list:       'backend_photos_list',
      public:     'frontend_photo_show',
      redirect:   'backend_photo_show',
      saveItem:   'api_v1_backend_photo_save_item',
      updateItem: 'api_v1_backend_photo_update_item'
    };

    /**
     * @inheritdoc
     */
    $scope.buildScope = function() {
      $scope.localize($scope.data.item, 'item');

      if ($scope.draftKey !== null && $scope.data.item.pk_content) {
        $scope.draftKey = 'photo-' + $scope.data.item.pk_content + '-draft';
      }

      $scope.checkDraft();
    };

    /**
     * @inheritdoc
     */
    $scope.hasMultilanguage = function() {
      return false;
    };
  }
]);
