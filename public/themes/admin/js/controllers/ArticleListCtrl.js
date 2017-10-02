(function () {
  'use strict';

  angular.module('BackendApp.controllers')
    /**
     * @ngdoc controller
     * @name  ArticleListCtrl
     *
     * @description
     *   description
     */
    .controller('ArticleListCtrl', [
      '$controller', '$location', '$scope', '$timeout', 'http', 'routing', 'messenger', 'localizer', 'linker', 'oqlEncoder',
      function($controller, $location, $scope, $timeout, http, routing, messenger, localizer, linker, oqlEncoder) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ListCtrl', {
          $scope:   $scope,
          $timeout: $timeout,
          routing:  routing
        }));

        /**
         * The criteria to search.
         *
         * @type Object
         */
        $scope.criteria = {
          content_type_name: 'article',
          epp: 10,
          in_litter: 0,
          orderBy: { created:  'desc' },
          page: 1
        };

        /**
         * The current locale.
         *
         * @type {String}
         */
        $scope.locale = 'es';

        /**
         * The localizer service.
         *
         * @type {Object}
         */
        $scope.localizer = localizer;

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
         * Initializes the content type for the current list.
         *
         * @param string locale The current locale.
         */
        $scope.init = function(lang, localize) {
          $scope.lang     = lang;
          $scope.localize = localize;

          if ($scope.localize && $scope.locale) {
            $scope.localizer.configure({
              keys: [ 'title', 'name' ],
              locales: [ 'es', 'gl' ]
            });

            $scope.il = linker.get([ 'title' ], $scope);
            $scope.cl = linker.get([ 'title' ], $scope);

            $scope.il.setKey($scope.locale);
            $scope.cl.setKey($scope.locale);
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

          var oql   = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: 'api_v1_backend_articles_list',
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            $scope.loading    = 0;
            $scope.data       = response.data;
            $scope.items      = response.data.results;
            $scope.categories = response.data.extra.categories;

            if ($scope.localize && $scope.locale) {
              $scope.categories = $scope.localizer
                .localize($scope.categories, $scope.locale);

              $scope.items = $scope.localizer
                .localize($scope.items, $scope.locale);

              $scope.il.link($scope.data.results, $scope.items);
              $scope.cl.link($scope.data.extra.categories, $scope.categories);
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
            $scope.il.setKey(nv);
            $scope.cl.setKey(nv);
            $scope.il.update();
            $scope.cl.update();
          }
        });
      }
    ]);
})();
