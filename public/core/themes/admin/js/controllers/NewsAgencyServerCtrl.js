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
      '$controller', '$scope', 'http', 'oqlEncoder',
      function($controller, $scope, http, oqlEncoder) {
        $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

        $scope.item = {
          authors_map: [],
          categories_map: [],
          filters: [],
          sync_from: '3600',
          external: 'none',
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
          list:       'backend_news_agency_server_list',
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
        $scope.$watch('data.extra.instance', function(nv) {
          if (nv) {
            $scope.item.url = 'https://' + nv + '.opennemas.com/ws/agency';
          }
        }, true);

        // Updates the URL for Opennemas News Agency when instance changes
        $scope.$watch('item.url', function(nv) {
          if (!$scope.data || !$scope.data.extra) {
            return;
          }

          $scope.data.extra.instance = /^https:\/\/.*\.opennemas\.com\/ws\/agency$/
            .test(nv) ? nv.replace('https://', '')
              .replace('.opennemas.com/ws/agency', '') : null;
        }, true);

        // Resets url and instance when type changes
        $scope.$watch('item.type', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if (nv && $scope.data && $scope.data.extra &&
              !$scope.data.extra.instance) {
            $scope.item.url = null;
          }
        }, true);

        // Resets external_link when external changes
        $scope.$watch('item.external', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if (nv && $scope.data && $scope.data.extra &&
              $scope.item.external !== 'redirect') {
            $scope.item.external_link = null;
          }
        }, true);

        // Update external when external_link is not empty (for old agencies)
        $scope.$watch('item.external_link', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if (nv && $scope.data && $scope.data.extra &&
              !$scope.data.extra.instance &&
              !($scope.item.external_link.length === 0)) {
            $scope.item.external = 'redirect';
          }
        }, true);

        $scope.getOnmAIPrompts = function() {
          $scope.waiting = true;
          var oqlQuery   = oqlEncoder.getOql({
            epp: 1000,
            mode: 'Transformation',
            orderBy: { name: 'asc' },
            page: 1,
          });

          http.get({ name: 'api_v1_backend_onmai_prompt_get_list', params: { oql: oqlQuery } })
            .then(function(response) {
              $scope.onmai_prompts = response.data.items;
              $scope.onmai_extras = response.data.extra;
            }).finally(function() {
              $scope.waiting = false;
            });
        };
        $scope.getOnmAIPrompts();
      }
    ]);
})();
