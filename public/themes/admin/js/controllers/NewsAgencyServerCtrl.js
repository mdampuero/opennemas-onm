(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  NewsAgencyServerCtrl
     *
     * @requires $controller
     * @requires $http
     * @requires $uibModal
     * @requires $scope
     * @requires itemService
     * @requires routing
     * @requires messenger
     *
     * @description
     *   Controller for server list in news agency.
     */
    .controller('NewsAgencyServerCtrl', [
      '$http', '$scope', 'routing', 'messenger',
      function($http, $scope, routing, messenger) {
        /**
         * @memberOf NewsAgencyServerCtrl
         *
         * @description
         *  The list of filters.
         *
         * @type {Array}
         */
        $scope.filters = [ '' ];

        /**
         * @memberOf NewsAgencyServerCtrl
         *
         * @description
         *  The list of categories map
         *
         * @type {Array}
         */
        $scope.categoriesMap = [{
            slug: '',
            id: null
        }];

        /**
         * @memberOf NewsAgencyServerCtrl
         *
         * @description
         *  Connection checked flag
         *
         * @type {Boolean}
         */
        $scope.test = false;

        /**
         * @memberOf NewsAgencyServerCtrl
         *
         * @description
         *  Opennemas agency flag
         *
         * @type {Boolean}
         */
        $scope.type = false;

        /**
         * @function addCategoryMap
         * @memberOf NewsAgencyServerCtrl
         *
         * @description
         *   Adds a new category map to the list.
         */
        $scope.addCategoryMap = function() {
          $scope.categoriesMap.push({
            slug: '',
            id: null
          });
        };

        /**
         * @function addFilter
         * @memberOf NewsAgencyServerCtrl
         *
         * @description
         *   Adds a new filter to the list.
         */
        $scope.addFilter = function() {
          $scope.filters.push('');
        };

        /**
         * @function check
         * @memberOf NewsAgencyServerCtrl
         *
         * @description
         *   Checks the connection to the server.
         */
        $scope.check = function() {
          $scope.checking = true;

          var url = routing.generate('backend_ws_news_agency_server_check', {
            password: $scope.password,
            url:      $scope.url,
            username: $scope.username
          });

          $http.get(url).then(function(response) {
            $scope.checking = false;
            $scope.test = true;
            messenger.post(response.data);
          }, function(response) {
            $scope.checking = false;
            messenger.post(response.data);
          });
        };

        /**
         * @function removeCategoryMap
         * @memberOf NewsAgencyServerCtrl
         *
         * @description
         *   Removes a category map from the list of categories map.
         *
         * @param {Integer} index The index of the filter to list.
         */
        $scope.removeCategoryMap = function(index) {
          $scope.categoriesMap.splice(index, 1);
        };

        /**
         * @function removeFilter
         * @memberOf NewsAgencyServerCtrl
         *
         * @description
         *   Removes a filter from the list of filters.
         *
         * @param {Integer} index The index of the filter to list.
         */
        $scope.removeFilter = function(index) {
          $scope.filters.splice(index, 1);
        };

        //
        $scope.$watch('categoriesMap', function(nv) {
          if (nv) {
            $scope.categoryJson = JSON.stringify(nv);
          }
        }, true);

        // Updates the URL for Opennemas News Agency the instance change
        $scope.$watch('instance', function(nv) {
          if (nv) {
            $scope.url = 'https://' + nv + '.opennemas.com/ws/agency';
          }
        }, true);

        // Initializes the instance value when the URL is initialized
        $scope.$watch('url', function(nv) {
          if (nv && !$scope.instance) {
            if (/https:\/\/(.*).opennemas.com\/ws\/agency/.test(nv)) {
              $scope.type     = true;
              $scope.instance = nv.replace('https://', '')
                .replace('.opennemas.com/ws/agency', '');
            }
          }
        }, true);
      }
    ]);
})();
