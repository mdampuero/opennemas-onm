(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  OpinionListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires messenger
     * @requires oqlEncoder
     * @requires queryManager
     *
     * @description
     *   Controller for opinion list.
     */
    .controller('OpinionListCtrl', [
      '$controller', '$location', '$scope', 'http', 'messenger', 'oqlEncoder',
      function($controller, $location, $scope, http, messenger, oqlEncoder) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestListCtrl', { $scope: $scope }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          content_type_name: 'opinion',
          epp: 10,
          in_litter: 0,
          orderBy: { starttime:  'desc' },
          page: 1
        };

        /**
         * @memberOf SubscriptionListCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          delete:         'api_v1_backend_opinion_delete',
          deleteSelected: 'api_v1_backend_opinions_delete',
          list:           'api_v1_backend_opinions_list',
          patch:          'api_v1_backend_opinion_patch',
          patchSelected:  'api_v1_backend_opinions_patch'
        };

        /**
         * @function init
         * @memberOf OpinionListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function(locale) {
          $scope.locale          = locale;
          $scope.columns.key     = 'opinion-columns';
          $scope.backup.criteria = $scope.criteria;

          $scope.criteria.orderBy = { created: 'asc' };

          oqlEncoder.configure({ placeholder: {
            title: '[key] ~ "%[value]%"'
          } });

          $scope.list();
        };

        /**
         * Updates the array of contents.
         *
         * @param string route Route name.
         */
        // $scope.list = function(route) {
        //   $scope.loading = 1;
        //   $scope.selected = { all: false, contents: [] };

        //   oqlEncoder.configure({
        //     placeholder: {
        //       title: 'title ~ "%[value]%"',
        //     }
        //   });

        //   var oql   = oqlEncoder.getOql($scope.criteria);
        //   var route = {
        //     name: $scope.route,
        //     params:  {
        //       contentType: $scope.criteria.content_type_name,
        //       oql: oql
        //     }
        //   };

        //   $location.search('oql', oql);

        //   http.get(route).then(function(response) {
        //     $scope.data     = response.data;
        //     $scope.total    = parseInt(response.data.total);
        //     $scope.contents = response.data.results;
        //     $scope.map      = response.data.map;

        //     if (response.data.hasOwnProperty('extra')) {
        //       $scope.extra = response.data.extra;
        //     }

        //     // Disable spinner
        //     $scope.loading = 0;
        //   }, function() {
        //     $scope.loading = 0;

        //     messenger.post({
        //       message: 'Error while fetching data from backend',
        //       type:    'error'
        //     });
        //   });
        // };
      }
    ]);
})();
