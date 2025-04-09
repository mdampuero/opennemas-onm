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
     * @requires localizer
     *
     * @description
     *   Handles all actions in user groups list.
     */
    .controller('AdvertisementListCtrl', [
      '$controller', '$scope', 'oqlEncoder', 'routing', '$location', 'http', 'messenger', 'localizer',
      function($controller, $scope, oqlEncoder, routing, $location, http, messenger, localizer) {
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
          patchItem: 'backend_ws_content_set_content_status',
          patchList: 'backend_ws_advertisements_update_batch',
        };

        /**
         * @memberOf AdvertisementListCtrl
         * @description
         *  Initialize the criteria for the filter
         */
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
              size: 'params regexp \'"devices"\\s*:\\s*{[^}]*"[value]"\\s*:\\s*1\''
            }
          });

          $scope.list();
        };

        /**
         * @function list
         * @memberOf AdvertisementListCtrl
         *
         * @description
         *  Get the list of advertisement
         */
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
            $scope.data  = response.data;
            $scope.total = parseInt(response.data.total);

            if ($scope.mode === 'grid') {
              $scope.contents = $scope.contents.concat(response.data.items);
            } else {
              $scope.contents = response.data.items;
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
         * Updates an item.
         *
         * @param int    index   Index of the item to update in contents.
         * @param int    id      Id of the item to update.
         * @param string route   Route name.
         * @param string name    Name of the property to update.
         * @param mixed  value   New value.
         * @param string loading Name of the property used to show work-in-progress.
         */
        $scope.patchItem = function(index, id, route, name, value, loading, reload) {
          // Load shared variable
          var contents = $scope.contents;

          // Enable spinner
          contents[index][loading] = 1;

          var route = {
            name:   $scope.routes.patchItem,
            params: {
              content_type_name: 'advertisement',
              id: id
            }
          };

          http.post(route, { value: value }).then(function(response) {
            contents[index][loading] = 0;
            contents[index][name] = response.data[name];
            messenger.post(response.data.messages);

            if (reload) {
              $scope.list($scope.route);
            }
          }, function(response) {
            contents[index][loading] = 0;
            messenger.post(response.data.messages);
          });

          // Updated shared variable
          $scope.contents = contents;
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
          $scope.tempCriteria.epp = $scope.criteria.epp;

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
