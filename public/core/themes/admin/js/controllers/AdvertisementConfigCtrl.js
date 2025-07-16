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

        $scope.originalOrder = [];

        $scope.treeOptions = {
          dropped: function(event) {
            $scope.extraads.forEach(function(item, index) {
              item.position = index;
            });
          }
        };

        /**
         * Initializes the ad configuration with domain and tag settings.
         *
         * @param {string} domain - The domain for which ads will be configured.
         * @param {string} tagsFormat - The format of tags to be used. If not a
         * valid format from smartAvailableTagsFormats, defaults to 'onecall_async'.
         * @param {Object} traffective - Configuration object for Traffective ads.
         * @param {Object} extraads - Additional ads configuration.
         */
        $scope.init = function(domain, tagsFormat, traffective, extraads) {
          if ($scope.smartAvailableTagsFormats.indexOf(tagsFormat) < 0) {
            tagsFormat = 'onecall_async';
          }

          $scope.extraads         = extraads;
          $scope.smart.domain             = domain;
          $scope.smart.tagsFormat         = tagsFormat;
          $scope.traffective.domain       = traffective.domain;
          $scope.traffective.clientAlias  = traffective.client_alias;
          $scope.traffective.dfpUrl       = traffective.dfpUrl;
          $scope.traffective.srcUrl       = traffective.srcUrl;
          $scope.traffective.ads          = traffective.ads;
          $scope.traffective.progAds      = traffective.progAds;
        };

        $scope.getAdPositions = function() {
          const positions = {};

          $scope.extraads.forEach(function(item) {
            positions[item.id] = parseInt(item.position);
          });

          return JSON.stringify(positions);
        };

        $scope.$watch('extraads', function(newOrder, oldOrder) {
          if (newOrder !== oldOrder) {
            newOrder.forEach(function(item, index) {
              item.position = index;
            });
          }
        }, true);
      }
    ]);
})();
