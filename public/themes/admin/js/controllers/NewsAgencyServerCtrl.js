(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  NewsAgencyServerCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     * @requires routing
     *
     * @description
     *   Controller for server list in news agency.
     */
    .controller('NewsAgencyServerCtrl', [
      '$controller', '$scope', 'http',
      function($controller, $scope, http) {
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        $scope.item = {
          authors_map: [],
          categories_map: [],
          filters: [],
          sync_from: '3600',
          type: '0'
        };

        /**
         * @memberOf AlbumCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          checkItem:  'api_v1_backend_news_agency_server_check_item',
          createItem: 'api_v1_backend_news_agency_server_create_item',
          getItem:    'api_v1_backend_news_agency_server_get_item',
          redirect:   'backend_news_agency_server_show',
          saveItem:   'api_v1_backend_news_agency_server_save_item',
          updateItem: 'api_v1_backend_news_agency_server_update_item'
        };

        /**
         * @function addFilter
         * @memberOf NewsAgencyServerCtrl
         *
         * @description
         *   Adds a new filter to the list.
         */
        $scope.addFilter = function() {
          $scope.item.filters.push('');
        };

        /**
         * @function addToMap
         * @memberOf NewsAgencyServerCtrl
         *
         * @description
         *   Adds a new empty item to a map.
         *
         * @param {String} name  The map name.
         */
        $scope.addToMap = function(name) {
          var property = name + '_map';

          if (!$scope.item[property]) {
            $scope.item[property] = [];
          }

          $scope.item[property].push({ slug: null, id: null });
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          if ($scope.item.activated) {
            $scope.item.activated = parseInt($scope.item.activated);
          }

          if ($scope.item.auto_import) {
            $scope.item.auto_import = parseInt($scope.item.auto_import);
          }
        };

        /**
         * @function check
         * @memberOf NewsAgencyServerCtrl
         *
         * @description
         *   Checks the connection to the server.
         */
        $scope.check = function() {
          $scope.flags.http.checking = true;

          var route = {
            name: $scope.routes.checkItem,
            params: {
              password: $scope.item.password,
              url:      $scope.item.url,
              username: $scope.item.username
            }
          };

          http.get(route).then(function() {
            $scope.disableFlags('http');
            $scope.status = 'success';
          }, function() {
            $scope.disableFlags('http');
            $scope.status = 'failure';
          });
        };

        /**
         * @inheritdoc
         */
        $scope.getData = function() {
          if (!$scope.item.categories_map ||
              $scope.item.categories_map.length === 0) {
            delete $scope.item.categories_map;
          }

          if (!$scope.item.authors_map ||
              $scope.item.authors_map.length === 0) {
            delete $scope.item.authors_map;
          }

          if (!$scope.item.filters ||
              $scope.item.filters.length === 0) {
            delete $scope.item.filters;
          }

          return $scope.item;
        };

        /**
         * @inheritdoc
         */
        $scope.hasMultilanguage = function() {
          return false;
        };

        /**
         * @function removeFilter
         * @memberOf NewsAgencyServerCtrl
         *
         * @description
         *   Removes a filter from the list of filters.
         *
         * @param {Integer} index The index of the filter to remove.
         */
        $scope.removeFilter = function(index) {
          $scope.item.filters.splice(index, 1);
        };

        /**
         * @function removeFromMap
         * @memberOf NewsAgencyServerCtrl
         *
         * @description
         *   Removes an item from a map.
         *
         * @param {String}  name  The map name.
         * @param {Integer} index The index of the item to remove.
         */
        $scope.removeFromMap = function(name, index) {
          var property = name + '_map';

          $scope.item[property].splice(index, 1);
        };

        // Updates the URL for Opennemas News Agency when instance changes
        $scope.$watch('instance', function(nv) {
          if (nv) {
            $scope.item.url = 'https://' + $scope.instance +
              '.opennemas.com/ws/agency';
          }
        }, true);

        // Resets url and instance when type changes
        $scope.$watch('item.type', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          $scope.item.url = null;
          $scope.instance = null;
          $scope.form.url.$setPristine(true);
        }, true);
      }
    ]);
})();
