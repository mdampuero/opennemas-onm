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
         * Traffective config object.
         * @type {Object}
         * @property {String} domain
         * @property {String} clientAlias
         * @property {String} dfpUrl
         */
        $scope.traffective = {};

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

        /**
         * @function init
         * @memberOf AdvertisementConfigCtrl
         * Method to init the advertisement config controller
         *
         * @param {String} domain The configured domain
         * @param {String} tagsFormat The configured tags format
         * @param {Object} traffective The traffective config object
         */
        $scope.init = function(domain, tagsFormat, traffective) {
          if ($scope.smartAvailableTagsFormats.indexOf(tagsFormat) < 0) {
            tagsFormat = 'onecall_async';
          }

          $scope.smart.domain             = domain;
          $scope.smart.tagsFormat         = tagsFormat;
          $scope.traffective.domain       = traffective.domain;
          $scope.traffective.clientAlias  = traffective.client_alias;
          $scope.traffective.dfpUrl       = traffective.dfpUrl;
          $scope.traffective.srcUrl       = traffective.srcUrl;
          $scope.traffective.ads          = traffective.ads;
          $scope.traffective.progAds      = traffective.progAds;
        };
      }
    ]);
})();
