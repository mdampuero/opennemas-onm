(function() {
  'use strict';

  angular.module('onm.ui.categoryInput', [ 'onm.http', 'onm.oql' ])
    .directive('onmCategoryInput', [
      '$window',
      function($window) {
        return {
          controller: 'OnmCategoryInputCtrl',
          restrict: 'E',
          scope: {
            autoGenerate:  '=?',
            generateFrom:  '=',
            maxCategorys:  '=',
            maxResults:    '=',
            locale:        '=',
            ngModel:       '=',
            placeholder:   '@',
            required:      '='
          },
          template: function() {
            return '<div class="tags-input-wrapper">' +
              '<tags-input add-from-autocomplete-only="true" display-property="name" key-property="id" min-length="2" ng-model="category" placeholder="[% placeholder %]" replace-spaces-with-dashes="false" ng-required="required">' +
                '<auto-complete debounce-delay="250" highlight-matched-text="true" max-results-to-show="[% maxResults + 1 %]" load-on-down-arrow="true" load-on-focus="true" load-on-empty="true" min-length="2" select-first-match="true" source="list($query)"></auto-complete>' +
              '</tags-input>' +
              '<i class="fa fa-circle-o-notch fa-spin tags-input-loading" ng-if="loading"></i>' +
            '</div>';
          }
        };
      }
    ])

    /**
     * @ngdoc controller
     * @name  OnmCategoryInputCtrl
     *
     * @requires $scope
     * @requires http
     *
     * @description
     *   List, checks and validates tags.
     */
    .controller('OnmCategoryInputCtrl', [
      '$q', '$scope', '$timeout', '$window', 'http', 'oqlEncoder',
      function($q, $scope, $timeout, $window, http, oqlEncoder) {
        /**
         * @memberOf OnmCategoryInputCtrl
         *
         * @description
         *  The list of category objects ready to use by category input directive.
         *
         * @type {Array}
         */
        $scope.category = null;

        /**
         * @function getCategory
         * @memberOf OnmCategoryInputCtrl
         *
         * @description
         *  Return a list of categories basing on a query string.
         *
         * @param {String} query The query string
         *
         * @returns {Object} The list of categories
         */
        $scope.list = function(query) {
          var criteria = {
            slug: query,
            epp: $scope.maxResults,
            orderBy: { 'length(name)': 'asc', name: 'asc' },
            page: 1
          };

          return http.get({
            name: 'api_v1_backend_tools_slug',
            params: { slug: query }
          }).then(function(response) {
            criteria.slug = response.data.slug;

            oqlEncoder.configure({
              placeholder: {
                slug: 'name ~ "%[value]%" or title ~ "%[value]%"',
              }
            });

            var oql = oqlEncoder.getOql(criteria);

            return http.get({
              name: 'api_v1_backend_category_get_list',
              params: { oql: oql }
            }).then(function(response) {
              $scope.data = response.data;

              var items = response.data.items;

              return $scope.localize(items);
            });
          });
        };

        /**
         * @function localize
         * @memberOf OnmCategoryInputCtrl
         *
         * @description
         *   Localizes the list of items based on the information included
         *
         * @param {Array|Object} items
         * @returns
         */
        $scope.localize = function(items) {
          if (!$scope.locale || !$scope.locale.multilanguage) {
            return items;
          }

          var currentLocale = $scope.locale.default;

          return items.map(function(item) {
            return {
              id: item.id,
              title: typeof item.title === 'object' ?
                item.title[currentLocale] ||
                item.title[Object.keys(item.title)[0]] ||
                '' :
                item.title ||
                '',
              name: typeof item.name === 'object' ?
                item.name[currentLocale] ||
                item.name[Object.keys(item.name)[0]] ||
                '' :
                item.name ||
                '',
              raw: item
            };
          });
        };

        /**
         * @function getJsonValue
         * @memberOf OnmTagsInputCtrl
         *
         * @description
         *   Returns the ngModel as JSON string.
         *
         * @return {String} The ngModel as JSON string.
         */
        $scope.getJsonValue = function() {
          return JSON.stringify($scope.ngModel);
        };

        $scope.$watch('ngModel', function(nv) {
          var ids = !$scope.category ? [] : $scope.category.map(function(e) {
            return e.id;
          });

          if (!nv || nv.length === 0 || angular.equals(nv, ids)) {
            return;
          }

          $scope.loading = true;

          var criteria = {
            id: nv,
            orderBy: { name: 'asc' },
          };

          oqlEncoder.configure({ placeholder: { id: '[key] in [[value]]' } });

          http.get({
            name: 'api_v1_backend_category_get_list',
            params: { oql: oqlEncoder.getOql(criteria) }
          }).then(function(response) {
            $scope.loading = false;

            if (response.data.items) {
              $scope.category = $scope.localize(response.data.items);
            }
          });
        }, true);

        $scope.$watch('category', function(nv) {
          $scope.ngModel = !nv || Array.isArray(nv) && !nv.length ? null : nv.map(function(e) {
            return e.id;
          });
        }, true);
      }
    ]);
})();
