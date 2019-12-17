/**
 * Handle actions for image inner.
 */
angular.module('BackendApp.controllers').controller('PhotoCtrl', [
  '$controller', '$rootScope', '$scope',
  function($controller, $rootScope, $scope) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * @memberOf PhotoCtrl
     *
     * @description
     *  The list of routes for the controller.
     *
     * @type {Object}
     */
    $scope.routes = {
      createItem: 'api_v1_backend_photo_create_item',
      getItem:    'api_v1_backend_photo_get_item',
      redirect:   'backend_photo_show',
      saveItem:   'api_v1_backend_photo_save_item',
      updateItem: 'api_v1_backend_photo_update_item'
    };

    /**
     * @function init
     * @memberOf ImageCtrl
     *
     * @description
     *    Method to init the image controller
     *
     * @param {object} photo The photo to edit.
     */
    $scope.init = function(photo, locale) {
      $scope.photo = photo;

      $scope.configure({ locale: locale });
    };
  }
]);
