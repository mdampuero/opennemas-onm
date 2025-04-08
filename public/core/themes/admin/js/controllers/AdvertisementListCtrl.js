(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  AdvertisementListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     * @requires routing
     * @requires $location
     * @requires http
     * @requires messenger
     *
     * @description
     *   Handles all actions in user groups list.
     */
    .controller('AdvertisementListCtrl', [
      '$controller', '$scope', 'oqlEncoder', 'routing', '$location', 'http', 'messenger',
      function($controller, $scope, oqlEncoder, routing, $location, http, messenger) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestListCtrl', { $scope: $scope }));

        /**
         * The criteria to search
         */
        $scope.criteria = {
          content_type_name: 'advertisement',
          epp: 10,
          in_litter: 0,
          orderBy: { created: 'desc' },
          page: 1,
        };

        /**
         * @memberOf AdvertisementListCtrl
         * @description
         *  The list of routes for the controller.
         * @type {Object}
         */
        $scope.routes = {
          getList: 'backend_ws_advertisements_list',
        };

        $scope.init = function() {
          $scope.backup.criteria = $scope.criteria;
          $scope.app.columns.hidden = [];

          oqlEncoder.configure({
            placeholder: {
              title: 'title ~ "%[value]%"',
              fk_content_categories: 'fk_content_categories regexp' +
                '"^[value]($|,)|,[value],|(^|,)[value]$"',
              starttime: 'starttime >= "[value]"',
              endtime: 'endtime <= "[value]"',
            }
          });

          $scope.list();
        };

        $scope.list = function() {
          if (!$scope.isModeSupported() || $scope.app.mode === 'list') {
            $scope.flags.http.loading = 1;
          } else {
            $scope.flags.http.loadingMore = 1;
          }

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: $scope.routes.getList,
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            $scope.data = response.data;

            if ($scope.mode === 'grid') {
              $scope.contents = $scope.contents.concat(response.data.results);
            } else {
              $scope.contents = response.data.results;
            }

            $scope.items = $scope.getContentsLocalizeTitle($scope.contents);
            $scope.map   = response.data.map;

            $scope.parseList(response.data);
            $scope.disableFlags('http');
          }, function(response) {
            messenger.post(response.data);
            $scope.disableFlags('http');
            $scope.data  = {};
            $scope.items = [];
          });
        };

        /**
         *  Localize all titles of the contents
         */
        $scope.getContentsLocalizeTitle = function() {
          if (!$scope.extra || !$scope.extra.options) {
            return $scope.contents;
          }

          var lz   = localizer.get($scope.extra.options);
          var keys = [ 'title' ];

          return lz.localize($scope.contents, keys, $scope.extra.options.default);
        };

        /**
         * @fucntion applyFilter
         * @memberOf AdvertisementListCtrl
         *
         * @description
         *  Apply the filter to the list of advertisement
         */
        $scope.applyFilter = function() {
          $scope.criteria = angular.copy($scope.tempCriteria);
        };

        /**
         * @function cancelFilter
         * @memberOf AdvertisementListCtrl
         *
         * @description
         *   Cancel the filter and reset the criteria.
         */
        $scope.cancelFilter = function() {
          $scope.tempCriteria.starttime = null;
          $scope.tempCriteria.endtime = null;

          $scope.criteria = angular.copy($scope.tempCriteria);
        };
      }
    ]);
})();
