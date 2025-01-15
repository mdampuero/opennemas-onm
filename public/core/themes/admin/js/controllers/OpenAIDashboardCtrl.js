(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  OpenAIDashboardCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires http
     * @requires messenger
     *
     * @description
     *   Provides actions to list notifications.
     */
    .controller('OpenAIDashboardCtrl', [
      '$controller', '$scope', 'http',
      function($controller, $scope, http) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

        /**
         * @memberOf OpenAIDashboardCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          getPricing: 'api_v1_backend_openai_get_pricing',
          getUsage: 'api_v1_backend_openai_get_usage',
        };

        $scope.totals = {
          words: 0,
          price: 0,
          usage: 0,
        };
        $scope.filterSelected = {
          year: null,
          month: null
        };
        $scope.panelSelected = 'words';

        /**
         * @function init
         * @memberOf OpenAIDashboardCtrl
         *
         * @description
         *   Initializes the form.
         */
        $scope.init = function() {
          http.get(
            {
              name: $scope.routes.getUsage,
              params: {
                year: $scope.filterSelected.year,
                month: $scope.filterSelected.month
              }
            })
            .then(function(response) {
              $scope.labels = response.data.labels;
              $scope.totals = response.data;
              $scope.filters = response.data.filters;
              if (!$scope.filterSelected.year) {
                $scope.indexFilter = 0;
                $scope.setFilter();
              }
              $scope.generateStats($scope.panelSelected);
              $scope.disableFlags('http');
            }, function() {
              $scope.disableFlags('http');
            });
        };

        $scope.generateStats = function(node) {
          $scope.panelSelected = node;
          $scope.data = $scope.totals[node].items;
          $scope.options = {
            responsive: true,
            height: '400px',
            maintainAspectRatio: false,
            aspectRatio: 2,
            scales: {
              xAxes: [
                {
                  barPercentage: 0.8,
                  categoryPercentage: 0.8
                }
              ],
              yAxes: [
                {
                  ticks: {
                    beginAtZero: true
                  }
                }
              ]
            }
          };
        };

        $scope.moveToPreviousMonths = function() {
          if ($scope.indexFilter < $scope.filters.length - 1) {
            $scope.indexFilter++;
            $scope.setFilter();
            $scope.init();
          }
        };

        $scope.moveToNextMonths = function() {
          if ($scope.indexFilter > 0) {
            $scope.indexFilter--;
            $scope.setFilter();
            $scope.init();
          }
        };

        $scope.setFilter = function() {
          $scope.labelFilter = $scope.filters[$scope.indexFilter].label + ' ' + $scope.filters[$scope.indexFilter].year;
          $scope.filterSelected = $scope.filters[$scope.indexFilter];
        };
      }
    ]);
})();
