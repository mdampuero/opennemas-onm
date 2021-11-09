(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  PollListCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires oqlEncoder
     *
     * @description
     *   Controller for poll list.
     */
    .controller('PollListCtrl', [
      '$controller', '$scope', '$window', 'oqlEncoder', 'routing',
      function($controller, $scope, $window, oqlEncoder, routing) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestListCtrl', { $scope: $scope }));

        /**
         * @inheritdoc
         */
        $scope.criteria = {
          content_type_name: 'poll',
          category_id: null,
          epp: 10,
          in_litter: 0,
          orderBy: { created: 'desc' },
          page: 1,
          tag: null
        };

        /**
         * @inheritdoc
         */
        $scope.routes = {
          deleteItem: 'api_v1_backend_poll_delete_item',
          deleteList: 'api_v1_backend_poll_delete_list',
          getList:    'api_v1_backend_poll_get_list',
          patchItem:  'api_v1_backend_poll_patch_item',
          patchList:  'api_v1_backend_poll_patch_list',
          public:     'frontend_poll_show',
        };

        /**
         * @function buildCharts
         * @memberOf PollListCtrl
         *
         * @description
         *   Builds an object with chart configuration basing on the list of
         *   items.
         *
         * @param {Array} items The list of items.
         */
        $scope.buildCharts = function(items) {
          $scope.chart = { data: [], labels: [] };

          for (var i = 0; i < items.length; i++) {
            $scope.chart.data.push(items[i].items.map(function(e) {
              return e.votes;
            }));

            $scope.chart.labels.push(items[i].items.map(function(e) {
              return e.item;
            }));
          }
        };

        /**
         * @function getFrontendUrl
         * @memberOf PollCtrl
         *
         * @description
         * Returns the frontend url for the content given its object
         *
         * @param  {String} item  The object item to generate the url from.
         * @return {String}
         */
        $scope.getFrontendUrl = function(item) {
          if (!$scope.categories) {
            return '';
          }

          var categories = $scope.categories.filter(function(e) {
            return e.id === item.categories[0];
          });

          if (categories.length === 0) {
            return '';
          }

          return $scope.getL10nUrl(
            routing.generate($scope.routes.public, {
              id: item.pk_content,
              created: $window.moment(item.created).format('YYYYMMDDHHmmss'),
              slug: item.slug,
              category_slug: categories[0].name
            })
          );
        };

        /**
         * @function init
         * @memberOf PollListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria    = $scope.criteria;
          $scope.app.columns.hidden = [];

          oqlEncoder.configure({
            placeholder: {
              title: '[key] ~ "%[value]%"'
            }
          });

          $scope.list();

          $scope.options = {
            tooltips: { enabled: false },
            elements: { arc: { borderWidth: 0 } },
          };
        };

        /**
         * @inheritdoc
         */
        $scope.parseList = function(data) {
          $scope.configure(data.extra);
          $scope.buildCharts(data.items);
          $scope.localize($scope.data.items, 'items');
          $scope.localize($scope.data.extra.categories, 'categories');
        };
      }
    ]);
})();
