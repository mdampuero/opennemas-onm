(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  ArticleListCtrl
     *
     * @requires $controller
     * @requires $location
     * @requires $scope
     * @requires $timeout
     * @requires http
     * @requires messenger
     * @requires linker
     * @requires localizer
     * @requires oqlEncoder
     *
     * @description
     *   Provides actions to list articles.
     */
    .controller('ArticleListCtrl', [
      '$controller', '$location', '$scope', '$timeout', 'http', 'messenger', 'linker', 'localizer', 'oqlEncoder',
      function($controller, $location, $scope, $timeout, http, messenger, linker, localizer, oqlEncoder) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope:   $scope,
          $timeout: $timeout
        }));

        /**
         * The criteria to search.
         *
         * @type {Object}
         */
        $scope.criteria = {
          content_type_name: 'article',
          epp: 10,
          in_litter: 0,
          orderBy: { created:  'desc' },
          page: 1
        };

        /**
         * @function groupCategories
         * @memberOf ArticleListCtrl
         *
         * @description
         *   Groups categories in the ui-select.
         *
         * @param {Object} item The category to group.
         *
         * @return {String} The group name.
         */
        $scope.groupCategories = function(item) {
          var category = $scope.categories.filter(function(e) {
            return e.pk_content_category === item.fk_content_category;
          });

          if (category.length > 0 && category[0].pk_content_category) {
            return category[0].title;
          }

          return '';
        };

        /**
         * @function init
         * @memberOf ArticleListCtrl
         *
         * @description
         *   Initializes services and list articles.
         *
         * @param {Boolean} localize Whether this content supports localization.
         */
        $scope.init = function(localize) {
          $scope.localize = localize;

          if ($scope.localize && $scope.locale) {
            $scope.ilz = localizer.get({
              keys: [ 'title', 'name' ],
              locales: [ 'es', 'gl' ]
            });

            $scope.cl = linker.get([ 'title' ], $scope);
            $scope.il = linker.get([ 'title' ], $scope);

            $scope.cl.setKey($scope.locale);
            $scope.il.setKey($scope.locale);
          }

          $scope.list();
        };

        /**
         * Updates the array of contents.
         */
        $scope.list = function() {
          $scope.loading  = 1;

          oqlEncoder.configure({
            placeholder: {
              title: 'title ~ "%[value]%"',
              fk_user_group: '[key] regexp "^[value],|^[value]$|,[value],|,[value]$"'
            }
          });

          var oql = oqlEncoder.getOql($scope.criteria);

          $location.search('oql', oql);

          return http.get({
            name: 'api_v1_backend_articles_list',
            params: { oql: oql }
          }).then(function(response) {
            $scope.loading    = 0;
            $scope.data       = response.data;
            $scope.items      = response.data.results;
            $scope.categories = response.data.extra.categories;

            if ($scope.localize && $scope.locale) {
              $scope.categories = $scope.ilz
                .localize($scope.categories, $scope.locale);

              $scope.items = $scope.ilz
                .localize($scope.items, $scope.locale);

              $scope.cl.link($scope.data.extra.categories, $scope.categories);
              $scope.il.link($scope.data.results, $scope.items);
            }

            // Scroll top
            $('body').animate({ scrollTop: '0px' }, 1000);
          }, function() {
            $scope.loading = 0;

            messenger.post({
              id: new Date().getTime(),
              message: 'Error while fetching data from backend',
              type: 'error'
            });
          });
        };

        // Localizes contents when locale changes
        $scope.$watch('locale', function(nv, ov) {
          if (nv === ov) {
            return;
          }

          if ($scope.localize && $scope.locale) {
            $scope.cl.setKey(nv);
            $scope.il.setKey(nv);
            $scope.cl.update();
            $scope.il.update();
          }
        });
      }
    ]);
})();
