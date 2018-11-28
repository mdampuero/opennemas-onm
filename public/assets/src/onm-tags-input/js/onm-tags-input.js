(function() {
  'use strict';

  angular.module('onm.tagsInput', [ 'onm.http' ])

    /**
     * @ngdoc directive
     * @name  onmTag
     *
     * @description
     *   Directive to create forms dynamically.
     */
    .directive('onmTagsInput', [
      '$window',
      function($window) {
        return {
          controller: 'OnmTagsInputCtrl',
          restrict: 'E',
          scope: {
            locale:       '=',
            ngModel:      '=',
            generateFrom: '=',
            autoGenerate: '=',
            placeholder:  '@',
          },
          template: function() {
            return '<div class="tags-input-buttons">' +
              '<button class="btn btn-info btn-mini pull-right" ng-click="generate(generateFrom())" type="button">' +
                '<i class="fa fa-refresh m-r-5" ng-class="{ \'fa-spin\': generating }"></i>' +
                $window.strings.tags.generate +
              '</button>' +
              '<button class="btn btn-danger btn-mini m-r-5 pull-right" ng-click="clear()" type="button">' +
                '<i class="fa fa-trash-o m-r-5"></i>' +
                $window.strings.tags.clear +
              '</button>' +
            '</div>' +
            '<div>' +
              '<tags-input add-from-autocomplete-only="false" ng-model="ngModel" display-property="name" on-tag-added="exists($tag)" on-tag-adding="validate($tag)" placeholder="[% placeholder %]" replace-spaces-with-dashes="false" tag-class="{ \'tag-item-exists\': $tag.id, \'tag-item-new\': !$tag.id }">' +
                '<auto-complete debounce-delay="250" highlight-matched-text="true" load-on-down-arrow="true" min-length="3" select-first-match="false" source="list($query)" template="tag"></auto-complete>' +
              '</tags-input>' +
              '<input name="tags" type="hidden" ng-value="getJsonValue()">' +
            '</div>' +
            '<script type="text/ng-template" id="tag">' +
              '<span ng-bind-html="$highlight($getDisplayText())"></span>' +
            '</script';
          }
        };
      }
    ])

    /**
     * @ngdoc controller
     * @name  OnmTagsInputCtrl
     *
     * @requires $scope
     * @requires http
     *
     * @description
     *   List, checks and validates tags.
     */
    .controller('OnmTagsInputCtrl', [
      '$scope', '$timeout', 'http',
      function($scope, $timeout, http) {
        /**
         * @function clear
         * @memberOf OnmTagsInputCtrl
         *
         * @description
         *   Removes all tags from the list.
         */
        $scope.clear = function() {
          $scope.ngModel = [];
        };

        /**
         * @function exists
         * @memberOf OnmTagsInputCtrl
         *
         * @description
         *   Checks if the added tag is already in the database and adds the id
         *   and the slug to the added tag.
         *
         * @param {Object} tag The added tag object.
         */
        $scope.exists = function(tag) {
          if (tag.id) {
            return;
          }

          var oql = 'name = "' + tag.name + '" order by name asc limit 1';

          http.get({
            name: 'api_v1_backend_tags_list',
            params: { oql: oql }
          }).then(function(response) {
            if (response.data.total === 1 &&
                tag.name === response.data.items[0].name) {
              for (var property in response.data.items[0]) {
                tag[property] = response.data.items[0][property];
              }

              return;
            }

            tag.language_id = $scope.locale;
          });
        };

        /**
         * @function generate
         * @memberOf OnmTagsInputCtrl
         *
         * @description
         *   Generates the list of tags basing on a string.
         *
         * @param {String} str The string to base on.
         */
        $scope.generate = function(str) {
          if (!str) {
            return;
          }

          $scope.generating = true;

          http.get({
            name: 'api_v1_backend_tools_tags',
            params: { q: str }
          }).then(function(response) {
            $scope.generating = false;

            if (!$scope.ngModel) {
              $scope.ngModel = response.data.items;
            }

            var ids = $scope.ngModel.map(function(e) {
              return e.id;
            });

            // Prevent duplicated tags
            var newTags = response.data.items.filter(function(e) {
              return ids.indexOf(e.id) === -1;
            });

            $scope.ngModel = $scope.ngModel.concat(newTags);
          }, function() {
            $scope.generating = false;
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

        /**
         * @function getTags
         * @memberOf OnmTagsInputCtrl
         *
         * @description
         *   Returns a list of tags basing on a query string.
         *
         * @param {String} query The query string.
         *
         * @return {Object} The list of tags.
         */
        $scope.list = function(query) {
          var oql = 'name ~ "%' + query + '%" order by name asc limit 10';

          return http.get({
            name: 'api_v1_backend_tags_list',
            params: { oql: oql }
          }).then(function(response) {
            return response.data.items;
          });
        };

        /**
         * @function validate
         * @memberOf OnmTagsInputCtrl
         *
         * @description
         *   Validates the tag ready to add to the component.
         *
         * @param {Object} tag The tag ready to add.
         *
         * @return {Object} A promise that returns true if the tag is valid or
         *                  false if the tag is not valid.
         */
        $scope.validate = function(tag) {
          if (tag.id) {
            return true;
          }

          return http.get({
            name: 'api_v1_backend_tags_validate',
            params: tag
          }).then(function() {
            return true;
          }, function() {
            return false;
          });
        };

        // Generates a new list of tags when generateFrom value changes
        $scope.$watch('autoGenerate', function(nv) {
          if (!nv) {
            return;
          }

          if ($scope.tm) {
            $timeout.cancel($scope.tm);
          }

          $scope.tm = $timeout(function() {
            $scope.autoGenerate = false;
            $scope.generate($scope.generateFrom());
          }, 250);
        }, true);
      }
    ]);
})();
