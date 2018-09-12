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
      '$controller', '$scope', '$uibModal',
      function($controller, $scope, $uibModal) {
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
        $scope.smartAvailableTagsFormats = [ 'onecall_async', 'onecall_sync' ];

        /**
         * @function init
         * @memberOf AdvertisementConfigCtrl
         * Method to init the advertisement config controller
         *
         * @param {String} domain The configured domain
         * @param {String} tagsFormat The configured tags format
         */
        $scope.init = function(domain, tagsFormat) {
          if ($scope.smartAvailableTagsFormats.indexOf(tagsFormat) < 0) {
            tagsFormat = 'onecall_async';
          }

          $scope.smart.domain = domain;
          $scope.smart.tagsFormat = tagsFormat;
        };

      }
    ]);
})();
