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
            hideGenerate:  '=',
            ignoreLocale:  '=',
            ignorePrivate: '=',
            locale:        '=',
            maxTags:       '=',
            maxResults:    '=',
            ngModel:       '=',
            placeholder:   '@',
            required:      '=',
            selectionOnly: '=',
            filter:        '=',
            exclude:       '='
          },
          template: function() {
            return '<div class="tags-input-wrapper">' +
              '<tags-input add-from-autocomplete-only="true" display-property="name" key-property="id" min-length="2" ng-model="categoriesInLocale" placeholder="[% placeholder %]" replace-spaces-with-dashes="false" ng-required="required" tag-class="{ \'tag-item-exists\': !isNewCategory($tag), \'tag-item-new\': isNewCategory($tag), \'tag-item-private\': $tag.private }">' +
                '<auto-complete debounce-delay="250" highlight-matched-text="true" max-results-to-show="[% maxResults + 1 %]" load-on-down-arrow="true" min-length="2" select-first-match="true" source="list($query)" template="categoryTemplate"></auto-complete>' +
              '</tags-input>' +
              '<i class="fa fa-circle-o-notch fa-spin tags-input-loading" ng-if="loading"></i>' +
              '<input name="categories" type="hidden" ng-value="getJsonValue()">' +
            '</div>' +
            '<script type="text/ng-template" id="categoryTemplate">' +
              '<span class="tag-item-text" ng-bind-html="$highlight($getDisplayText())"></span>' +
              '<span class="badge badge-success pull-right text-uppercase" ng-if="$parent.$parent.$parent.$parent.$parent.isNewCategory(data)">' +
                '<strong> Test </strong></span>' +
              '<span class="badge badge-private pull-right text-uppercase m-l-10" ng-if="data.private"><strong> Test private </strong></span>' +
            '</script>';
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
        $scope.categoriesInLocale = Array.isArray($scope.ngModel) ? $scope.ngModel : [];

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

              return items;
            });
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
      }
    ]);
})();
