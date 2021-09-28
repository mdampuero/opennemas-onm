(function() {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.ui.tagsInput
   *
   * @description
   *   The `onm.ui.tagsInput` module provides a directive and a controller to
   *   create tags-input control with autocomplete and multilanguage support.
   */
  angular.module('onm.ui.tagsInput', [ 'onm.http', 'onm.oql' ])

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
            autoGenerate:  '=?',
            generateFrom:  '=',
            hideGenerate:  '=',
            ignoreLocale:  '=',
            locale:        '=',
            maxTags:       '=',
            maxResults:    '=',
            ngModel:       '=',
            placeholder:   '@',
            required:      '=',
            selectionOnly: '=',
            filter:        '=',
          },
          template: function() {
            return '<div class="tags-input-buttons">' +
              '<button ng-if="!filter" class="btn btn-info btn-mini pull-right" ng-hide="hideGenerate" ng-click="generate(generateFrom())" type="button">' +
                '<i class="fa fa-refresh m-r-5" ng-class="{ \'fa-spin\': generating }"></i>' +
                $window.strings.tags.generate +
              '</button>' +
              '<button ng-if="!filter" class="btn btn-danger btn-mini m-r-5 pull-right" ng-click="clear()" type="button">' +
                '<i class="fa fa-trash-o m-r-5"></i>' +
                $window.strings.tags.clear +
              '</button>' +
              '<span ng-if="!filter" class="tags-input-counter badge badge-default pull-right" ng-class="{ \'badge-danger\': tagsInLocale.length == maxTags, \'badge-warning text-default\': tagsInLocale.length > maxTags/2 && tagsInLocale.length < maxTags }">' +
                '[% tagsInLocale ? tagsInLocale.length : 0 %] / [% maxTags %]' +
              '</span>' +
            '</div>' +
            '<div class="tags-input-wrapper">' +
              '<tags-input add-from-autocomplete-only="true" display-property="name" key-property="id" min-length="2" ng-model="tagsInLocale" on-tag-removing="remove($tag, filter)" on-tag-adding="add($tag, filter)" placeholder="[% placeholder %]" replace-spaces-with-dashes="false" ng-required="required" tag-class="{ \'tag-item-exists\': !isNewTag($tag), \'tag-item-new\': isNewTag($tag) }">' +
                '<auto-complete ng-if="!filter" debounce-delay="250" highlight-matched-text="true" max-results-to-show="[% maxResults + 1 %]" load-on-down-arrow="true" min-length="2" select-first-match="false" source="list($query)" template="tag"></auto-complete>' +
                '<auto-complete ng-if="filter" debounce-delay="250" highlight-matched-text="true" max-results-to-show="[% maxResults + 1 %]" load-on-down-arrow="true" min-length="2" select-first-match="false" source="list($query)"></auto-complete>' +
              '</tags-input>' +
              '<i class="fa fa-circle-o-notch fa-spin tags-input-loading" ng-if="loading"></i>' +
              '<input name="tags" type="hidden" ng-value="getJsonValue()">' +
            '</div>' +
            '<script type="text/ng-template" id="tag">' +
              '<span class="tag-item-text" ng-bind-html="$highlight($getDisplayText())"></span>' +
              '<span class="badge badge-success pull-right text-uppercase" ng-if="$parent.$parent.$parent.$parent.$parent.isNewTag(data)">' +
                '<strong>' + $window.strings.tags.newItem + '</strong>' +
              '</span>' +
              '<span class="badge badge-default pull-right" ng-class="{ \'badge-danger\': !$parent.$parent.$parent.$parent.$parent.data.extra.stats[data.id] }" ng-show="!$parent.$parent.$parent.$parent.$parent.isNewTag(data)">' +
                '<strong>[% $parent.$parent.$parent.$parent.$parent.data.extra.stats[data.id] ? $parent.$parent.$parent.$parent.$parent.data.extra.stats[data.id] : 0 %]</strong>' +
              '</span>' +
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
      '$q', '$scope', '$timeout', '$window', 'http', 'oqlEncoder',
      function($q, $scope, $timeout, $window, http, oqlEncoder) {
        /**
         * @memberOf OnmTagsInputCtrl
         *
         * @description
         *  The list of tag objects ready to use by tags-input directive.
         *
         * @type {Array}
         */
        $scope.tags = null;

        /**
         * @function clear
         * @memberOf OnmTagsInputCtrl
         *
         * @description
         *   Removes all tags from the list.
         */
        $scope.clear = function() {
          $scope.tagsInLocale = [];
        };

        /**
         * @function add
         * @memberOf OnmTagsInputCtrl
         *
         * @description
         *   Checks if the added tag is already in the database and adds the id
         *   and the slug to the added tag.
         *
         * @param {Object} tag     The added tag object.
         * @param {boolean} filter A flag to indicate if the directive is used to filter.
         */
        $scope.add = function(tag, filter) {
          if (filter) {
            var input       = document.querySelector('tags-input input');
            var placeholder = input.getAttribute('placeholder');

            input.setAttribute('disabled', true);
            input.setAttribute('placeholder', '');
            input.setAttribute('data-placeholder', placeholder);

            return;
          }

          if ($scope.tags && $scope.tags.length >= $scope.maxTags) {
            return false;
          }

          if ($scope.isNewTag(tag)) {
            var t = angular.extend({}, tag);

            delete t.id;

            return $scope.validate(t);
          }

          return true;
        };

        /**
         * @function remove
         * @memberOf OnmTagsInputCtrl
         *
         * @description
         * Removes a tag.
         *
         * @param {Object} tag     The added tag object.
         * @param {boolean} filter A flag to indicate if the directive is used to filter.
         */
        $scope.remove = function(tag, filter) {
          if (!filter) {
            return;
          }

          var input = document.querySelector('tags-input input');

          input.removeAttribute('disabled');
          input.setAttribute('placeholder', input.getAttribute('data-placeholder'));
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

            if (!$scope.tags) {
              $scope.tags = response.data.items;
            }

            var ids = $scope.tags.map(function(e) {
              return e.id;
            });

            // Prevent duplicated tags
            var newTags = response.data.items.filter(function(e) {
              return ids.indexOf(e.id) === -1;
            });

            $scope.tags         = $scope.tags.concat(newTags);
            $scope.tagsInLocale = angular.copy($scope.tags);

            if ($scope.locale && $scope.locale.multilanguage) {
              $scope.tagsInLocale = $scope.tags.filter(function(e) {
                return !$scope.locale || !e.locale ||
                  e.locale === $scope.locale.selected;
              });
            }
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
         * @function isNewTag
         * @memberOf OnmTagsInputCtrl
         *
         * @description
         *   Checks if the tag id is numeric to determine if it is a new tag or
         *   the tag already exists.
         *
         * @param {Object} tag The tag to check.
         *
         * @return {Boolean} True if the tag is new. False otherwise.
         */
        $scope.isNewTag = function(tag) {
          return !angular.isNumber(tag.id);
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
          var criteria = {
            name: query,
            epp: $scope.maxResults,
            orderBy: { 'length(name)': 'asc', name: 'asc' },
            page: 1
          };

          if (!$scope.ignoreLocale && $scope.locale &&
              $scope.locale.multilanguage) {
            criteria.locale = $scope.locale.selected;
          }

          oqlEncoder.configure({
            placeholder: {
              name: '[key] ~ "%[value]%"',
              locale: '([key] is null or [key] = "[value]")'
            }
          });

          var oql = oqlEncoder.getOql(criteria);

          return http.get({
            name: 'api_v1_backend_tag_get_list',
            params: { oql: oql }
          }).then(function(response) {
            $scope.data = response.data;

            var items = response.data.items;

            if (!$scope.selectionOnly) {
              var found = items.filter(function(e) {
                return e.name === query;
              });

              if (found.length === 0) {
                var item = { id: query, name: query };

                if ($scope.locale && $scope.locale.multilanguage) {
                  item.locale = $scope.locale.selected;
                }

                items.push(item);

                items = items.sort(function(a, b) {
                  return a.name.length < b.name.length ? -1 : 0;
                });
              }
            }

            return items;
          });
        };

        /**
         * @function saveTag
         * @memberOf OnmTagsInputCtrl
         *
         * @description
         *   Saves a new tag basing on data.
         *
         * @param {Object} data The tag information.
         *
         * @return {Integer} The tag id.
         */
        $scope.saveTag = function(data) {
          var route = { name: 'api_v1_backend_tag_save_item' };

          delete data.id;

          return http.post(route, data).then(function(response) {
            var id = response.headers().location
              .substring(response.headers().location.lastIndexOf('/') + 1);

            return parseInt(id);
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
          // If selected from the autocomplete
          if (!$scope.isNewTag(tag)) {
            return true;
          }

          return http.get({
            name: 'api_v1_backend_tag_validate_item',
            params: tag
          }).then(function() {
            return true;
          }, function() {
            return false;
          });
        };

        // Saves new tags and executes callbacks in args
        $scope.$on('onmTagsInput.save', function(event, args) {
          var existingTags = $scope.tags.filter(function(e) {
            return !$scope.isNewTag(e);
          }).map(function(e) {
            return e.id;
          });

          var newTags = $scope.tags.filter(function(e) {
            return $scope.isNewTag(e);
          });

          for (var i = 0; i < newTags.length; i++) {
            newTags[i] = $scope.saveTag(newTags[i]);
          }

          $q.all(newTags).then(function(ids) {
            if (args.onSuccess) {
              args.onSuccess(existingTags.concat(ids));
            }
          }, function() {
            if (args.onError) {
              args.onError();
            }
          });
        });

        // Generates a new list of tags when generateFrom value changes
        $scope.$watch('autoGenerate', function(nv) {
          if (!nv || !$scope.tags || $scope.tags.length > 0) {
            $scope.autoGenerate = false;
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

        // Updates ngModel when tags added/removed
        $scope.$watch('locale', function(nv) {
          $scope.tagsInLocale = $scope.tags;

          if (nv && nv.multilanguage && angular.isArray($scope.tags)) {
            $scope.tagsInLocale = $scope.tags.filter(function(e) {
              return !e.locale || e.locale === nv.selected;
            });
          }
        }, true);

        // Gets the list of tags when ngModel changes
        $scope.$watch('ngModel', function(nv) {
          var ids = !$scope.tags ? [] : $scope.tags.map(function(e) {
            return e.id;
          });

          // No ngModel or same items in tags and ngModel
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
            name: 'api_v1_backend_tag_get_list',
            params: { oql: oqlEncoder.getOql(criteria) }
          }).then(function(response) {
            $scope.loading = false;

            if (response.data.items) {
              $scope.tags         = response.data.items;
              $scope.tagsInLocale = angular.copy($scope.tags);

              if ($scope.locale && $scope.locale.multilanguage) {
                $scope.tagsInLocale = $scope.tags.filter(function(e) {
                  return !$scope.locale || !e.locale ||
                    e.locale === $scope.locale.selected;
                });
              }
            }
          });
        }, true);

        // Updates ngModel when tags added/removed
        $scope.$watch('tags', function(nv) {
          $scope.ngModel = !nv || Array.isArray(nv) && !nv.length ? null : nv.map(function(e) {
            return e.id;
          });
        }, true);

        // Updates ngModel when tags added/removed
        $scope.$watch('tagsInLocale', function(nv, ov) {
          var nvIds = [];
          var ovIds = [];

          if (nv) {
            nvIds = nv.map(function(e) {
              return e.id;
            });
          }

          if (ov) {
            ovIds = ov.filter(function(e) {
              // Only delete tags for any or current locale
              return !$scope.locale || !e.locale ||
                e.locale === $scope.locale.selected;
            }).map(function(e) {
              return e.id;
            });
          }

          var toAdd    = _.difference(nvIds, ovIds);
          var toDelete = _.difference(ovIds, nvIds);

          if (toDelete.length > 0) {
            $scope.tags = $scope.tags.filter(function(e) {
              return toDelete.indexOf(e.id) === -1;
            });

            if ($scope.tags.length === 0) {
              $scope.tags = null;
            }
          }

          if (toAdd.length > 0) {
            if (!$scope.tags) {
              $scope.tags = [];
            }

            if (!$scope.ngModel) {
              $scope.ngModel = [];
            }

            $scope.tags = $scope.tags.concat(nv.filter(function(e) {
              return toAdd.indexOf(e.id) !== -1 &&
                $scope.ngModel.indexOf(e.id) === -1;
            }));
          }
        }, true);
      }
    ]);
})();
