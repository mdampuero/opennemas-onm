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
     * @memberOf PhotoCtrl
     *
     * @description
     *  Flag to enabled or disable drafts.
     *
     * @type {Boolean}
     */
    $scope.draftEnabled = true;

    /**
     * @memberOf PhotoCtrl
     *
     * @description
     *  The draft key.
     *
     * @type {String}
     */
    $scope.draftKey = 'photo-draft';

    /**
     * @memberOf PhotoCtrl
     *
     * @description
     *  The timeout function for draft.
     *
     * @type {Function}
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
