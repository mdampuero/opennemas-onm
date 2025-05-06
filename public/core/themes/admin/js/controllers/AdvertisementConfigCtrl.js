(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  AdvertisementConfigCtrl
     *
     * @requires $controller
     * @requires $scope
     *
     * @description
     *   Provides actions to edit, save and update articles.
     */
    .controller('AdvertisementConfigCtrl', [
      '$controller', '$scope', '$uibModal', '$rootScope', 'http',
      function($controller, $scope, $uibModal, $rootScope, http) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * Smart config object.
         *
         * @type {Object}
         */
        $scope.smart = {};

        /**
         * The available smart tags format
         *
         * @type {Array}
         */
        $scope.smartAvailableTagsFormats = [
          'onecall_async',
          'ajax_async',
          'onecall_sync'
        ];

        $scope.treeOptions = {
          dragMove: function(event) {
            // TODO: Implement drag move logic
          },
          dropped: function(event) {
            // Todo: Implement dropped logic
          }
        };

        $scope.init = function(domain, tagsFormat, extraads) {
          if ($scope.smartAvailableTagsFormats.indexOf(tagsFormat) < 0) {
            tagsFormat = 'onecall_async';
          }

          $scope.extraads         = extraads;
          $scope.smart.domain     = domain;
          $scope.smart.tagsFormat = tagsFormat;
        };
      }
    ]);
})();
