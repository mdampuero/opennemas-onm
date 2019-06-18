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
      '$controller', '$scope', 'oqlEncoder',
      function($controller, $scope, oqlEncoder) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestListCtrl', { $scope: $scope }));

        /**
         * @inheritdoc
         */
        $scope.criteria = {
          content_type_name: 'poll',
          pk_fk_content_category: null,
          epp: 10,
          in_litter: 0,
          orderBy: { created: 'desc' },
          page: 1
        };

        /**
         * @inheritdoc
         */
        $scope.routes = {
          delete:         'api_v1_backend_poll_delete',
          deleteSelected: 'api_v1_backend_polls_delete',
          list:           'api_v1_backend_polls_list',
          patch:          'api_v1_backend_poll_patch',
          patchSelected:  'api_v1_backend_polls_patch'
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
         * @function init
         * @memberOf PollListCtrl
         *
         * @description
         *   Configures the controller.
         */
        $scope.init = function() {
          $scope.backup.criteria = $scope.criteria;

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
