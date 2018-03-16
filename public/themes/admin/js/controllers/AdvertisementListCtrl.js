(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  TrashListCtrl
     *
     * @requires $controller
     * @requires $http
     * @requires $uibModal
     * @requires $scope
     * @requires localizer
     * @requires messenger
     *
     * @description
     *   Controller for Trash list.
     */
    .controller('AdvertisementListCtrl', [
      '$controller', 'http', '$uibModal', '$scope', 'localizer', 'messenger',
      function($controller, http, $uibModal, $scope, localizer, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentListCtrl', { $scope: $scope }));
      }
    ]);
})();
